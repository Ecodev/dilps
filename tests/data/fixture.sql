INSERT INTO `user` (`id`, `login`, `email`) VALUES
  (1000, 'Alice', 'alice@example.com');

INSERT INTO `collection` (`id`, `name`) VALUES
  (2000, 'Test collection 2000');

INSERT INTO `artist` (`id`, `name`) VALUES
  (3000, 'Test artist 3000');

INSERT INTO `tag` (`id`, `name`) VALUES
  (4000, 'Test tag 4000');

INSERT INTO `institution` (`id`, `name`) VALUES
  (5000, 'Test institution 5000');

INSERT INTO `image` (`id`, `collection_id`, `name`, `filename`) VALUES
  (6000, 2000, 'Test image 6000', 'dw4jV3zYSPsqE2CB8BcP8ABD0.jpg');

INSERT INTO `image_artist` (`image_id`, `artist_id`) VALUES
  (6000, 3000);

INSERT INTO `image_tag` (`image_id`, `tag_id`) VALUES
  (6000, 4000);
