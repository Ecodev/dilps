-- This scripts will migrate from the old data structure to the new one.
-- It assumes that old tables and new tables are in the same database, side-by-side.
-- New tables must already exists and be up-to-date.
--
-- A quick way to ensure all those assumptions and do the migration are the following commands:
--
--     ./vendor/bin/doctrine orm:schema-tool:drop --full-database --force
--     ./vendor/bin/doctrine-migrations migrations:migrate --ansi --no-interaction
--     more data/cache/old.sql | mysql -u dilps -p dilps
--     more bin/migrate_old_data.sql | mysql -u dilps -p dilps

ALTER TABLE `ng_meta`
  ADD INDEX (`institution`),
  ADD INDEX (`name1`),
  ADD INDEX (`name2`),
  ADD INDEX (metaeditor),
  ADD INDEX (metacreator);
ALTER TABLE `ng_meta_additional`
  ADD INDEX (`parentid`),
  ADD INDEX (`institution`),
  ADD INDEX (`name1`),
  ADD INDEX (`name2`);
ALTER TABLE `ng_collection`
  ADD INDEX (`sammlung_ort`);
ALTER TABLE ng_licence
  ADD INDEX (utilisateur);
ALTER TABLE ng_img_tag
  ADD INDEX (tag);
ALTER TABLE `ng_geocodes` ADD INDEX(`address`);
ALTER TABLE `ng_location` ADD INDEX(`id`);

-- Procedure to try as best as we can to explode concatenated ID and migrate them into proper FK
-- However some of those IDs don't exist anymore and will thus be ignored
DROP PROCEDURE IF EXISTS createRelationBetweenCollectionAndImage;
DELIMITER //

CREATE PROCEDURE createRelationBetweenCollectionAndImage()
  BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE collectionId INT;
    DECLARE imageIds TEXT;
    DECLARE cur1 CURSOR FOR SELECT
                              2000 + id,
                              imageid FROM ng_panier
                            WHERE imageid != '';
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    OPEN cur1;

    read_loop: LOOP
      FETCH cur1
      INTO collectionId, imageIds;
      IF done
      THEN
        LEAVE read_loop;
      END IF;

      -- Build a string like "(1, 2001),(2, 2001),(3, 2001)"
      SET @values = REPLACE(REPLACE(REPLACE(REPLACE(imageIds, 'x', ''), ' ', ''), '\n', ''), ',',
                            CONCAT(', ', collectionId, '),('));
      SET @values = CONCAT('(', @values, ', ', collectionId, ')');

      -- Debug
      SELECT @values;

      -- Build INSERT statement
      SET @insert = CONCAT('INSERT IGNORE INTO collection_image (image_id, collection_id) VALUES ', @values);

      -- Execute INSERT statement
      PREPARE stmt FROM @insert;
      EXECUTE stmt;
      DEALLOCATE PREPARE stmt;


    END LOOP;

    CLOSE cur1;
  END;
//

DELIMITER ;


START TRANSACTION;

INSERT INTO institution (name)
  SELECT institution FROM ng_meta
  UNION DISTINCT
  SELECT institution FROM ng_meta_additional
  UNION DISTINCT
  SELECT CONVERT(CAST(institution AS BINARY) USING utf8mb4) FROM ng_users
  UNION DISTINCT
  SELECT sammlung_ort FROM ng_collection;

-- Delete a few invalid institution
DELETE FROM institution
WHERE name IN ('', '-', '--', '?', 'A EFFACER', 'X', 'xx');

INSERT INTO artist (name)
  SELECT DISTINCT name
  FROM ng_artist;

-- Complete missing artists from meta
INSERT INTO artist (name)
  SELECT DISTINCT name1 FROM ng_meta
  WHERE name1 != '' AND name1 NOT IN (SELECT name FROM artist);

INSERT INTO artist (name)
  SELECT DISTINCT name2 FROM ng_meta
  WHERE name2 != '' AND name2 NOT IN (SELECT name FROM artist);

-- Delete a few invalid artists
DELETE FROM artist
WHERE name IN ('', '-', '--', '?', 'A EFFACER');

INSERT INTO collection (id, name, description, sorting, is_source)
  SELECT
    ng_collection.collectionid,
    ng_collection.name,
    ng_collection.descr,
    ng_collection.indexfeld,
    TRUE
  FROM ng_collection;

UPDATE collection
  JOIN ng_collection ON collection.id = ng_collection.collectionid
  JOIN institution ON institution.name = ng_collection.sammlung_ort
SET collection.institution_id = institution.id;


INSERT INTO image (id, filename, creation_date, update_date, width, height, file_size)
  SELECT
    CONCAT(collectionid, imageid),
    CONCAT(collectionid, imageid, filename),
    insert_date,
    modify_date,
    width,
    height,
    size
  FROM ng_img;

INSERT INTO dating (image_id, `from`, `to`)
  SELECT
    CONCAT(collectionid, imageid),
    `from`,
    `to`
  FROM ng_dating
  WHERE
    CONCAT(collectionid, imageid) IN (SELECT id FROM image);


INSERT INTO collection (id, name)
  SELECT
    1000 + ng_group.id,
    CONVERT(CAST(name AS BINARY) USING utf8mb4)
  FROM ng_group;

UPDATE collection
  JOIN ng_group ON collection.id = 1000 + ng_group.parentid
SET parent_id = 1000 + ng_group.id;


INSERT INTO user (id, creation_date, login, password, email, is_administrator, active_until, type)
  SELECT
    id,
    date_ajout,
    utilisateur,
    motdepasse,
    mail,
    groupe = 'archivmaster',
    IF(validite = '', NULL, STR_TO_DATE(CONCAT('01-', validite), '%d-%m-%Y')),
    IF(type = 'externe', 'default', 'unil')
  FROM ng_users;

-- Update image with their institution
UPDATE user
  JOIN ng_users ON user.id = ng_users.id
  JOIN institution
    ON CONVERT(institution.name USING utf8mb4) = CONVERT(CAST(ng_users.institution AS BINARY) USING utf8mb4)
SET user.institution_id = institution.id;

-- Inject non-existing user based on their agreement
INSERT INTO user (login, terms_agreement, type)
  SELECT
    utilisateur,
    date,
    'unil'
  FROM ng_licence
  WHERE utilisateur NOT IN (SELECT login FROM user);

-- Update existing user with their agreement
UPDATE user
  JOIN ng_licence ON user.login = ng_licence.utilisateur
SET user.terms_agreement = ng_licence.date;

-- Update group user with their owner
UPDATE collection
  JOIN ng_group ON collection.id = 1000 + ng_group.id
  JOIN user ON user.login = ng_group.owner
SET collection.creator_id = user.id;


INSERT INTO collection_image (collection_id, image_id)
  SELECT
    1000 + groupid,
    CONCAT(collectionid, imageid)
  FROM ng_img_group;

INSERT INTO tag (name) SELECT DISTINCT tag FROM ng_img_tag;

INSERT INTO image_tag (image_id, tag_id)
  SELECT
    CONCAT(ng_img.collectionid, ng_img.imageid),
    tag.id
  FROM ng_img_tag
    JOIN ng_img ON ng_img.imageid = ng_img_tag.imageid
    JOIN tag ON tag.name = ng_img_tag.tag;

UPDATE image
  JOIN ng_meta ON CONCAT(ng_meta.collectionid, ng_meta.imageid) = image.id
  LEFT JOIN ng_location ON ng_meta.locationid = ng_location.id
SET
  image.type             = ng_meta.type,
  image.status           = ng_meta.status,
  image.addition         = ng_meta.addition,
  image.name             = ng_meta.title,
  image.expanded_name    = ng_meta.title_expanded,
  image.dating           = ng_meta.dating,
  image.material         = ng_meta.material,
  image.technique        = ng_meta.technique,
  image.format           = ng_meta.format,
  image.literature       = ng_meta.literature,
  image.page             = ng_meta.page,
  image.figure           = ng_meta.figure,
  image.table            = ng_meta.table,
  image.isbn             = ng_meta.isbn,
  image.creation_date    = ng_meta.insert_date,
  image.update_date      = ng_meta.modify_date,
  image.comment          = ng_meta.commentary,
  image.rights           = ng_meta.imagerights,
  image.museris_url      = ng_meta.museris_link,
  image.museris_cote     = ng_meta.museris_cote,
  image.technique_author = ng_meta.auteur_technique,
  image.locality         = IFNULL(ng_location.location, ''),
  image.area             = IFNULL(ng_location.hierarchy, '');

UPDATE image
  JOIN ng_meta ON image.id = CONCAT(ng_meta.collectionid, ng_meta.imageid)
  JOIN institution ON institution.name = ng_meta.institution
SET image.institution_id = institution.id;

-- Link image to artist 1
REPLACE INTO image_artist (image_id, artist_id)
  SELECT
    CONCAT(ng_meta.collectionid, ng_meta.imageid),
    artist.id
  FROM ng_meta
    JOIN artist ON artist.name = ng_meta.name1;

-- Link image to artist 2
REPLACE INTO image_artist (image_id, artist_id)
  SELECT
    CONCAT(ng_meta.collectionid, ng_meta.imageid),
    artist.id
  FROM ng_meta
    JOIN artist ON artist.name = ng_meta.name2;

-- Link image to creator
UPDATE image
  JOIN ng_meta ON CONCAT(ng_meta.collectionid, ng_meta.imageid) = image.id
  JOIN user ON user.login = ng_meta.metacreator
SET
  image.creator_id = user.id;

-- Link image to updater
UPDATE image
  JOIN ng_meta ON CONCAT(ng_meta.collectionid, ng_meta.imageid) = image.id
  JOIN user ON user.login = ng_meta.metaeditor
SET
  image.updater_id = user.id;


INSERT INTO collection (id, creation_date, name)
  SELECT
    2000 + id,
    date,
    CONVERT(CAST(motclef AS BINARY) USING utf8mb4)
  FROM ng_panier;

-- Link collection to creator
UPDATE collection
  JOIN ng_panier ON 2000 + ng_panier.id = collection.id
  JOIN user ON user.login = ng_panier.utilisateur
SET
  collection.creator_id = user.id;

CALL createRelationBetweenCollectionAndImage;

INSERT INTO image (
  original_id,
  type,
  status,
  addition,
  name,
  expanded_name,
  dating,
  material,
  technique,
  format,
  literature,
  page,
  figure,
  `table`,
  isbn,
  creation_date,
  update_date,
  comment,
  rights,
  museris_url,
  museris_cote,
  technique_author,
  locality,
  area
)
  SELECT
    CONCAT(ng_meta.collectionid, ng_meta.imageid),
    ng_meta.type,
    ng_meta.status,
    ng_meta_additional.addition,
    ng_meta_additional.title,
    ng_meta_additional.title_expanded,
    ng_meta_additional.dating,
    ng_meta_additional.material,
    ng_meta_additional.technique,
    ng_meta_additional.format,
    ng_meta_additional.literature,
    ng_meta_additional.page,
    ng_meta_additional.figure,
    ng_meta_additional.table,
    ng_meta_additional.isbn,
    ng_meta.insert_date,
    ng_meta.modify_date,
    ng_meta.commentary,
    ng_meta.imagerights,
    ng_meta.museris_link,
    ng_meta.museris_cote,
    ng_meta.auteur_technique,
    IFNULL(ng_location.location, ''),
    IFNULL(ng_location.hierarchy, '')

  FROM ng_meta_additional
    JOIN ng_meta ON ng_meta.imageid = ng_meta_additional.parentid
    LEFT JOIN ng_location ON ng_meta_additional.locationid = ng_location.id;

UPDATE image
  JOIN ng_meta ON image.original_id = CONCAT(ng_meta.collectionid, ng_meta.imageid)
  JOIN ng_meta_additional ON ng_meta.imageid = ng_meta_additional.parentid
  JOIN institution ON institution.name = ng_meta_additional.institution
SET image.institution_id = institution.id;

-- Link image to artist 1
REPLACE INTO image_artist (image_id, artist_id)
  SELECT
    image.id,
    artist.id
  FROM ng_meta_additional
    JOIN artist ON artist.name = ng_meta_additional.name1
    JOIN ng_meta ON ng_meta.imageid = ng_meta_additional.parentid
    JOIN image ON image.original_id = CONCAT(ng_meta.collectionid, ng_meta.imageid);

-- Link image to artist 2
REPLACE INTO image_artist (image_id, artist_id)
  SELECT
    image.id,
    artist.id
  FROM ng_meta_additional
    JOIN artist ON artist.name = ng_meta_additional.name2
    JOIN ng_meta ON ng_meta.imageid = ng_meta_additional.parentid
    JOIN image ON image.original_id = CONCAT(ng_meta.collectionid, ng_meta.imageid);

-- Ensure that we don't leave invalid empty values
UPDATE image SET type = 'default' WHERE type = '';

-- Delete a few invalid locality
UPDATE image SET locality = '' WHERE locality IN('-', '?', '.');

-- Remove would-be duplicate, then fix encoding
DELETE FROM ng_geocodes WHERE address != CONVERT(CAST(address AS BINARY) USING utf8mb4) AND CONVERT(CAST(address AS BINARY) USING utf8mb4) IN (SELECT * FROM (SELECT address FROM ng_geocodes) AS tmp);
UPDATE ng_geocodes SET address = CONVERT(CAST(address AS BINARY) USING utf8mb4);

-- First, attempt to match coordinates with institution, which should be the most precise
UPDATE image
  JOIN institution ON image.institution_id = institution.id
  JOIN ng_geocodes ON CONCAT(image.locality, ', ', institution.name) = ng_geocodes.address
SET image.latitude = ng_geocodes.lat,
  image.longitude  = ng_geocodes.lon
WHERE image.locality != '' AND image.longitude IS NULL AND image.latitude IS NULL;

-- Then, attempt to match coordinates with only the locality
UPDATE image
  JOIN ng_geocodes ON CONCAT(image.locality, ', ') = ng_geocodes.address
SET image.latitude = ng_geocodes.lat,
  image.longitude  = ng_geocodes.lon
WHERE image.locality != '' AND image.longitude IS NULL AND image.latitude IS NULL;

-- Attempt to extract country from old hierarchy if no known country yet
UPDATE image
SET country_id =
IF(INSTR(area, '| France |'), 2,
   IF(INSTR(area, '| Italia |'), 15,
      IF(INSTR(area, 'Bolivia |'), 54,
         IF(INSTR(area, '| United States |'), 30,
            IF(INSTR(area, '| Congo |'), 84,
               IF(INSTR(area, '| Australia |'), 3,
                  IF(INSTR(area, '| Sryah |'), 223,
                     IF(INSTR(area, '| Nihon |'), 16,
                        IF(INSTR(area, '| eská Republika |'), 7,
                           IF(INSTR(area, '| Polska |'), 22,
                              IF(INSTR(area, '| España |'), 26,
                                 IF(INSTR(area, '| Deutschland |'), 10,
                                    IF(INSTR(area, '| Nederland |'), 19,
                                       IF(INSTR(area, '| Norge |'), 21,
                                          IF(INSTR(area, '| België |'), 5,
                                             IF(INSTR(area, '| Österreich |'), 4,
                                                IF(INSTR(area, '| Suomi |'), 9,
                                                   IF(INSTR(area, 'Danmark |'), 8,
                                                      IF(INSTR(area, '| United Kingdom |'), 29,
                                                         IF(INSTR(area, '| Ellás |'), 11,
                                                            IF(INSTR(area, '| Schweiz |'), 1,
                                                               country_id)
                                                         )
                                                      )
                                                   )
                                                )
                                             )
                                          )
                                       )
                                    )
                                 )
                              )
                           )
                        )
                     )
                  )
               )
            )
         )
      )
   )
) WHERE image.country_id IS NULL;

-- Normalize a few things to have a better chance to match
UPDATE image
SET locality =
REPLACE(
    REPLACE(
        REPLACE(
            REPLACE(
                REPLACE(
                    REPLACE(
                        REPLACE(
                            REPLACE(
                                REPLACE(
                                    REPLACE(
                                        REPLACE(locality, ' (Florida)', ' (Florida - États-Unis)'),
                                        ' (Syrie) (?)', ' (Syrie)'),
                                    ', USA)', ', États-Unis)'),
                                ' (India)', ' (Inde)'),
                            ', D)', ', Allemagne)'),
                        ' (New Zealand)', ' (Nouvelle-Zélande)'),
                    ' (Corée)', ' (Corée du Sud)'),
                ' (Lybie)', ' (Libye)'),
            ' (Allmagne)', ' (Allemagne)'),
        ' Rébublique Tchèque)', ' République tchèque)'),
    ' République Tchèque', ' République tchèque')
WHERE locality != '';

-- Attempt to extract country from old location if no known country yet
-- In this case we assume that the country is the last word at the end of the string enclosed by parenthesis
UPDATE image
  JOIN country ON INSTR(image.locality, CONCAT(country.name, ')'))
SET image.country_id = country.id
WHERE locality != '' AND image.country_id IS NULL;

-- Attempt to extract country from old location if no known country yet
-- In this case we assume that the country is the first word at the end of the string enclosed by parenthesis
UPDATE image
  JOIN country ON INSTR(image.locality, CONCAT('(', country.name))
SET image.country_id = country.id
WHERE locality != '' AND image.country_id IS NULL;

COMMIT;

DROP PROCEDURE createRelationBetweenCollectionAndImage;
