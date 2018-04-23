-- This script will generate a bash script to move files from their old location to the new one.
-- This script should be used on the old DB. And the generated bash script might be manually tweaked
-- and executed at a later time, most likely just before/right after the switch to production of the new app.
--
-- Typical usage would be:
--
--     more bin/generate-script-to-migrate-files.sql | mysql --raw -u dilps -p dilps

SELECT CONCAT(
           'mv "',
           ng_img_base.base,
           '/',
           REPLACE(REPLACE(filename, "\\'", "'"), "$", "\\$"),
           '" ',
           '"data/images/',
           ng_img.collectionid,
           imageid,
           REPLACE(REPLACE(filename, "\\'", "'"), "$", "\\$"),
           '"'
       ) AS '#!/usr/bin/env bash'
FROM ng_img
  JOIN ng_collection ON ng_img.collectionid = ng_collection.collectionid
  JOIN ng_img_base ON ng_collection.collectionid = ng_img_base.collectionid
ORDER BY ng_img_base.base, ng_img.filename, ng_img.imageid;
