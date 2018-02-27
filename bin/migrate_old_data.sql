-- This scripts will migrate from the old data structure to the new one.
-- It assumes that old tables and new tables are in the same database, side-by-side.
-- New tables must already exists and be up-to-date.
--
-- A quick way to ensure all those assumptions and do the migration are the following commands:
--
--     ./vendor/bin/doctrine orm:schema-tool:drop --full-database --force
--     ./vendor/bin/doctrine-migrations migrations:migrate --no-interaction
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
DROP PROCEDURE IF EXISTS createRelationBetweenCollectionAndCard;
DELIMITER //

CREATE PROCEDURE createRelationBetweenCollectionAndCard()
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
      SET @insert = CONCAT('INSERT IGNORE INTO collection_card (card_id, collection_id) VALUES ', @values);

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


INSERT INTO card (id, filename, creation_date, update_date, width, height, file_size)
  SELECT
    CONCAT(collectionid, imageid),
    CONCAT(collectionid, imageid, filename),
    insert_date,
    modify_date,
    width,
    height,
    size
  FROM ng_img;

INSERT INTO dating (card_id, `from`, `to`)
  SELECT
    CONCAT(collectionid, imageid),
    `from`,
    `to`
  FROM ng_dating
  WHERE
    CONCAT(collectionid, imageid) IN (SELECT id FROM card);


INSERT INTO collection (id, name)
  SELECT
    1000 + ng_group.id,
    CONVERT(CAST(name AS BINARY) USING utf8mb4)
  FROM ng_group;

UPDATE collection
  JOIN ng_group ON collection.id = 1000 + ng_group.parentid
SET parent_id = 1000 + ng_group.id;


INSERT INTO user (id, creation_date, login, password, email, role, active_until, type)
  SELECT
    id,
    date_ajout,
    utilisateur,
    motdepasse,
    mail,
    IF(groupe = 'archivmaster', 'administrator', 'student'),
    IF(validite = '', NULL, STR_TO_DATE(CONCAT('01-', validite), '%d-%m-%Y')),
    IF(type = 'externe', 'default', 'unil')
  FROM ng_users;

-- Update card with their institution
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


INSERT INTO collection_card (collection_id, card_id)
  SELECT
    1000 + groupid,
    CONCAT(collectionid, imageid)
  FROM ng_img_group;

INSERT INTO tag (name) SELECT DISTINCT tag FROM ng_img_tag;

INSERT INTO card_tag (card_id, tag_id)
  SELECT
    CONCAT(ng_img.collectionid, ng_img.imageid),
    tag.id
  FROM ng_img_tag
    JOIN ng_img ON ng_img.imageid = ng_img_tag.imageid
    JOIN tag ON tag.name = ng_img_tag.tag;

UPDATE card
  JOIN ng_meta ON CONCAT(ng_meta.collectionid, ng_meta.imageid) = card.id
  LEFT JOIN ng_location ON ng_meta.locationid = ng_location.id
SET
  card.status           = ng_meta.status,
  card.addition         = ng_meta.addition,
  card.name             = ng_meta.title,
  card.expanded_name    = ng_meta.title_expanded,
  card.dating           = ng_meta.dating,
  card.material         = ng_meta.material,
  card.technique        = ng_meta.technique,
  card.format           = ng_meta.format,
  card.literature       = ng_meta.literature,
  card.page             = ng_meta.page,
  card.figure           = ng_meta.figure,
  card.table            = ng_meta.table,
  card.isbn             = ng_meta.isbn,
  card.creation_date    = ng_meta.insert_date,
  card.update_date      = ng_meta.modify_date,
  card.comment          = ng_meta.commentary,
  card.rights           = ng_meta.imagerights,
  card.museris_url      = ng_meta.museris_link,
  card.museris_cote     = ng_meta.museris_cote,
  card.technique_author = ng_meta.auteur_technique,
  card.locality         = IFNULL(ng_location.location, ''),
  card.area             = IFNULL(ng_location.hierarchy, '');

UPDATE card
  JOIN ng_meta ON card.id = CONCAT(ng_meta.collectionid, ng_meta.imageid)
  JOIN institution ON institution.name = ng_meta.institution
SET card.institution_id = institution.id;

-- Link card to artist 1
REPLACE INTO card_artist (card_id, artist_id)
  SELECT
    CONCAT(ng_meta.collectionid, ng_meta.imageid),
    artist.id
  FROM ng_meta
    JOIN artist ON artist.name = ng_meta.name1;

-- Link card to artist 2
REPLACE INTO card_artist (card_id, artist_id)
  SELECT
    CONCAT(ng_meta.collectionid, ng_meta.imageid),
    artist.id
  FROM ng_meta
    JOIN artist ON artist.name = ng_meta.name2;

-- Link card to creator
UPDATE card
  JOIN ng_meta ON CONCAT(ng_meta.collectionid, ng_meta.imageid) = card.id
  JOIN user ON user.login = ng_meta.metacreator
SET
  card.creator_id = user.id;

-- Link card to updater
UPDATE card
  JOIN ng_meta ON CONCAT(ng_meta.collectionid, ng_meta.imageid) = card.id
  JOIN user ON user.login = ng_meta.metaeditor
SET
  card.updater_id = user.id;


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

CALL createRelationBetweenCollectionAndCard;

-- Insert related cards
INSERT INTO card (
  original_id,
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
  locality,
  area
)
  SELECT
    CONCAT(ng_meta.collectionid, ng_meta.imageid),
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
    IFNULL(ng_location.location, ''),
    IFNULL(ng_location.hierarchy, '')

  FROM ng_meta_additional
    JOIN ng_meta ON ng_meta.imageid = ng_meta_additional.parentid
    LEFT JOIN ng_location ON ng_meta_additional.locationid = ng_location.id;

-- Link related card to institution
UPDATE card
  JOIN ng_meta ON card.original_id = CONCAT(ng_meta.collectionid, ng_meta.imageid)
  JOIN ng_meta_additional ON ng_meta.imageid = ng_meta_additional.parentid
  JOIN institution ON institution.name = ng_meta_additional.institution
SET card.institution_id = institution.id;

-- Link related card to artist 1
REPLACE INTO card_artist (card_id, artist_id)
  SELECT
    card.id,
    artist.id
  FROM ng_meta_additional
    JOIN artist ON artist.name = ng_meta_additional.name1
    JOIN ng_meta ON ng_meta.imageid = ng_meta_additional.parentid
    JOIN card ON card.original_id = CONCAT(ng_meta.collectionid, ng_meta.imageid);

-- Link related card to artist 2
REPLACE INTO card_artist (card_id, artist_id)
  SELECT
    card.id,
    artist.id
  FROM ng_meta_additional
    JOIN artist ON artist.name = ng_meta_additional.name2
    JOIN ng_meta ON ng_meta.imageid = ng_meta_additional.parentid
    JOIN card ON card.original_id = CONCAT(ng_meta.collectionid, ng_meta.imageid);

-- Link related card as proper related cards...
INSERT INTO card_card (card_source, card_target)
  SELECT
    card.id,
    card.original_id
  FROM card
  WHERE original_id IS NOT NULL;

-- ... both way
INSERT INTO card_card (card_source, card_target)
  SELECT
    card.original_id,
    card.id
  FROM card
  WHERE original_id IS NOT NULL;

-- Remove the temporary fake relation
UPDATE card SET original_id = NULL;

-- Delete a few invalid locality
UPDATE card SET locality = '' WHERE locality IN('-', '?', '.');

-- Remove would-be duplicate, then fix encoding
DELETE FROM ng_geocodes WHERE address != CONVERT(CAST(address AS BINARY) USING utf8mb4) AND CONVERT(CAST(address AS BINARY) USING utf8mb4) IN (SELECT * FROM (SELECT address FROM ng_geocodes) AS tmp);
UPDATE ng_geocodes SET address = CONVERT(CAST(address AS BINARY) USING utf8mb4);

-- First, attempt to match coordinates with institution, which should be the most precise
UPDATE card
  JOIN institution ON card.institution_id = institution.id
  JOIN ng_geocodes ON CONCAT(card.locality, ', ', institution.name) = ng_geocodes.address
SET card.latitude = ng_geocodes.lat,
  card.longitude  = ng_geocodes.lon
WHERE card.locality != '' AND card.longitude IS NULL AND card.latitude IS NULL;

-- Then, attempt to match coordinates with only the locality
UPDATE card
  JOIN ng_geocodes ON CONCAT(card.locality, ', ') = ng_geocodes.address
SET card.latitude = ng_geocodes.lat,
  card.longitude  = ng_geocodes.lon
WHERE card.locality != '' AND card.longitude IS NULL AND card.latitude IS NULL;

-- Attempt to extract country from old hierarchy if no known country yet
UPDATE card
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
) WHERE card.country_id IS NULL;

-- Normalize a few things to have a better chance to match
UPDATE card
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
UPDATE card
  JOIN country ON INSTR(card.locality, CONCAT(country.name, ')'))
SET card.country_id = country.id
WHERE locality != '' AND card.country_id IS NULL;

-- Copy address from card to institution if all cards of that institution have exactly the same address
UPDATE institution
  JOIN card ON institution.id = card.institution_id
SET
  institution.locality   = card.locality,
  institution.area       = card.area,
  institution.latitude   = card.latitude,
  institution.longitude  = card.longitude,
  institution.country_id = card.country_id
WHERE card.institution_id IN (
  SELECT tmp.institution_id FROM (
     SELECT
       institution_id,
       COUNT(institution_id) AS institutionCount,
       COUNT(DISTINCT CONCAT_WS(';', locality, area, IFNULL(latitude, 'NULL'), IFNULL(longitude, 'NULL'), IFNULL(country_id, 'NULL'))) AS localityCount
     FROM card
     WHERE institution_id IS NOT NULL
     GROUP BY institution_id
   ) AS tmp
  WHERE tmp.localityCount = 1
);

-- Remove address from card if it is exactly the same as its institution
UPDATE card
  JOIN institution ON institution.id = card.institution_id
    AND institution.locality = card.locality
    AND institution.area = card.area
    AND (institution.latitude = card.latitude OR (institution.latitude IS NULL AND card.latitude IS NULL))
    AND (institution.longitude = card.longitude OR (institution.longitude IS NULL AND card.longitude IS NULL))
    AND (institution.country_id = card.country_id OR (institution.country_id IS NULL AND card.country_id IS NULL))
    SET
      card.locality   = '',
      card.area       = '',
      card.latitude   = NULL,
      card.longitude  = NULL,
      card.country_id = NULL;

COMMIT;

DROP PROCEDURE createRelationBetweenCollectionAndCard;
