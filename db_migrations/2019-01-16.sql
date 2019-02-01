CREATE TABLE `sma_regions` (
  `id` int(11) NOT NULL,
  `code` varchar(55) NOT NULL,
  `name` varchar(55) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `sma_regions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id` (`id`);

ALTER TABLE `sma_regions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `sma_users`
  ADD `region_id` INT NOT NULL AFTER `type`;