INSERT INTO `user` (`id`, `login`, `email`) VALUES
  (1000, 'Alice', 'alice@example.com');

INSERT INTO `collection` (`id`, `name`) VALUES
  (2000, 'Test collection 2000');

INSERT INTO `image` (`id`, `collection_id`, `name`, `filename`) VALUES
  (3000, 2000, 'Test image 3000', 'dw4jV3zYSPsqE2CB8BcP8ABD0.jpg');
