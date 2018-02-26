INSERT INTO `user` (`id`, `login`, `email`, password) VALUES
  (1000, 'Alice', 'alice@example.com', MD5('money'));

INSERT INTO `collection` (`id`, `name`, `description`) VALUES
  (2000, 'Test collection 2000', 'Roads? Where we''re going we don''t need roads.'),
  (2001, 'Test collection 2001', 'Hello. My name is Inigo Montoya. You killed my father. Prepare to die.');

INSERT INTO `artist` (`id`, `name`) VALUES
  (3000, 'Test artist 3000');

INSERT INTO `tag` (`id`, `name`) VALUES
  (4000, 'Test tag 4000');

INSERT INTO `institution` (`id`, `country_id`, `name`) VALUES
  (5000, 1, 'Test institution 5000');

INSERT INTO `card` (`id`, `original_id`, `name`, `filename`, `width`, `height`, file_size, dating) VALUES
  (6000, NULL, 'Test card 6000', 'dw4jV3zYSPsqE2CB8BcP8ABD0.jpg', 960, 425, 90188, '2000'),
  (6001, NULL, 'Test suggestion card 6001', 'dw4jV3zYSPsqE2CB8BcP8ABD0.jpg', 960, 425, 90188, ''),
  (6002, 6000, 'Test suggestion card 6002', 'dw4jV3zYSPsqE2CB8BcP8ABD0.jpg', 960, 425, 90188, ''),
  (6003, NULL, 'Test card 6003', 'dw4jV3zYSPsqE2CB8BcP8ABD0.jpg', 960, 425, 90188, ''),
  (6004, 6000, 'Test card 6004', 'dw4jV3zYSPsqE2CB8BcP8ABD0.jpg', 960, 425, 90188, ''),
  (6005, 6000, 'Test related card 6005', 'dw4jV3zYSPsqE2CB8BcP8ABD0.jpg', 960, 425, 90188, '');

INSERT INTO `card_artist` (`card_id`, `artist_id`) VALUES
  (6000, 3000);

INSERT INTO `card_tag` (`card_id`, `tag_id`) VALUES
  (6000, 4000);

INSERT INTO `collection_card` (`collection_id`, `card_id`) VALUES
  (2000, 6000),
  (2001, 6001),
  (2001, 6002);

INSERT INTO `change` (`id`, `original_id`, `suggestion_id`, `type`, `request`) VALUES
  (7000, NULL, 6001, 'create', 'I want to add new card to collection'),
  (7001, 6000, 6002, 'update', 'I want to edit existing card'),
  (7002, 6000, NULL, 'delete', 'I want to delete existing card');

INSERT INTO `dating` (`id`, `card_id`, `from`, `to`) VALUES
  (8000, 6000, '2451545', '2451545');

INSERT INTO `card_card` (`card_source`, `card_target`) VALUES
  (6000, 6005),
  (6005, 6000);
