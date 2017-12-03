/*
-- Query: SELECT * FROM mcs.users
LIMIT 0, 1000

-- Date: 2017-12-04 00:14
*/
INSERT INTO `users` (`id`,`first_name`,`last_name`,`email`,`password_hash`,`is_active`,`type`,`deleted`,`created`) VALUES (3,'Pavel','Admin','admin@email.cz',NULL,'1',4,'0','2017-10-02 23:32:42');
INSERT INTO `users` (`id`,`first_name`,`last_name`,`email`,`password_hash`,`is_active`,`type`,`deleted`,`created`) VALUES (10,'Petr','Autor','pb@email.cz',NULL,'1',1,'0','2017-10-02 23:52:54');
INSERT INTO `users` (`id`,`first_name`,`last_name`,`email`,`password_hash`,`is_active`,`type`,`deleted`,`created`) VALUES (11,'Honza','Recenzent','pb2@email.cz',NULL,'1',2,'0','2017-10-02 23:55:30');
INSERT INTO `users` (`id`,`first_name`,`last_name`,`email`,`password_hash`,`is_active`,`type`,`deleted`,`created`) VALUES (12,'Martin','Duna','mmmm@email.cz',NULL,'1',2,'0','2017-10-02 23:56:22');
INSERT INTO `users` (`id`,`first_name`,`last_name`,`email`,`password_hash`,`is_active`,`type`,`deleted`,`created`) VALUES (13,'David','Bowie','david@google.cz',NULL,'1',2,'0','2017-10-02 23:59:36');
INSERT INTO `users` (`id`,`first_name`,`last_name`,`email`,`password_hash`,`is_active`,`type`,`deleted`,`created`) VALUES (14,'Josef','Dvorak','pepa@dvorak.cz',NULL,'1',1,'0','2017-10-03 00:11:18');



/*
-- Query: SELECT * FROM mcs.posts
LIMIT 0, 1000

-- Date: 2017-12-04 00:12
*/
INSERT INTO `posts` (`id`,`title`,`slug`,`author_id`,`abstract`,`file`,`file_name`,`published`,`published_by_id`,`deleted`,`created`) VALUES (1,'TÃ½den letnÃ­ informatiky','3-tyden-letni-informatiky',3,'DruhÃ½ srpnovÃ½ tÃ½den se na katedÅ™e informatiky a vÃ½poÄetnÃ­ techniky Fakulty aplikovanÃ½ch vÄ›d ZÄŒU konal ÄtvrtÃ½ roÄnÃ­k letnÃ­ho soustÅ™edÄ›nÃ­ TyLIDi neboli TÃ½den letnÃ­ informatiky pro dÃ­vky. LetoÅ¡nÃ­ho roÄnÃ­ku, kterÃ½ se konal ve dnech 7. aÅ¾ 11. srpna, se zÃºÄastnilo 9 studentek stÅ™ednÃ­ch Å¡kol.',?,'','2017-11-21 18:48:35',3,0,'2017-11-20 00:29:07');
INSERT INTO `posts` (`id`,`title`,`slug`,`author_id`,`abstract`,`file`,`file_name`,`published`,`published_by_id`,`deleted`,`created`) VALUES (2,'Ekonomickou fakultu povede Michaela KrechovskÃ¡','3-ekonomickou-fakultu-povede-michaela-krechovska',3,'Fakultu ekonomickou (FEK) ZÃ¡padoÄeskÃ© univerzity v Plzni povede od bÅ™ezna 2018 dalÅ¡Ã­ ÄtyÅ™i roky Michaela KrechovskÃ¡, dosavadnÃ­ vedoucÃ­ katedry financÃ­ a ÃºÄetnictvÃ­. KandidÃ¡tkou na dÄ›kanku ji ve stÅ™edu 15. listopadu odpoledne zvolil v tajnÃ© volbÄ› AkademickÃ½ senÃ¡t fakulty. Vyslovilo se pro ni deset z Å¡estnÃ¡cti pÅ™Ã­tomnÃ½ch senÃ¡torÅ¯.  \r\n',?,'chapter_09 (1).pdf','2017-11-21 18:48:35',3,0,'2017-11-20 14:49:22');
INSERT INTO `posts` (`id`,`title`,`slug`,`author_id`,`abstract`,`file`,`file_name`,`published`,`published_by_id`,`deleted`,`created`) VALUES (3,'Ventusky','10-ventusky',10,'WebovÃ¡ Aplikace Ventusky, vyvinutÃ¡ studenty doktorskÃ©ho studia na ZÃ¡padoÄeskÃ© univerzitÄ› v Plzni (ZÄŒU) Davidem a Martinem PrantlovÃ½mi ve spoluprÃ¡ci s Markem MojzÃ­kem, dokÃ¡Å¾e nynÃ­ pÅ™edpovÃ­dat poÄasÃ­ v EvropÄ› jeÅ¡tÄ› pÅ™esnÄ›ji. ',?,'',NULL,NULL,0,'2017-11-21 15:15:35');
INSERT INTO `posts` (`id`,`title`,`slug`,`author_id`,`abstract`,`file`,`file_name`,`published`,`published_by_id`,`deleted`,`created`) VALUES (4,'HokejovÃ¡ Bitva o PlzeÅˆ je tu!','14-hokejova-bitva-o-plzen-je-tu',14,'Ve stÅ™edu 22. listopadu v 19:45 hodin vypukne na ledovÃ© ploÅ¡e Home Monitoring ArÃ©ny Bitva o PlzeÅˆ. PopÃ¡tÃ© se proti sobÄ› postavÃ­ hokejovÃ½ tÃ½m ZÃ¡padoÄeskÃ© univerzity a LÃ©kaÅ™skÃ© fakulty Univerzity Karlovy v Plzni.\r\n',?,'latex1.pdf',NULL,NULL,0,'2017-11-21 19:05:47');
INSERT INTO `posts` (`id`,`title`,`slug`,`author_id`,`abstract`,`file`,`file_name`,`published`,`published_by_id`,`deleted`,`created`) VALUES (5,'PlzeÅˆskÃ½ kraj a ZÄŒU','10-plzensky-kraj-a-zu',10,'Hejtman PlzeÅˆskÃ©ho kraje Josef Bernard a rektor ZÃ¡padoÄeskÃ© univerzity (ZÄŒU) Miroslav HoleÄek podepsali v pÃ¡tek 24. listopadu na krajskÃ©m ÃºÅ™adÄ› memorandum, kterÃ© rozÅ¡iÅ™uje a konkretizuje oblasti a formy spoluprÃ¡ce obou institucÃ­.',?,'8._Webovy_server.pdf','2017-12-03 22:08:59',3,0,'2017-12-03 21:09:30');



/*
-- Query: SELECT * FROM mcs.scores
LIMIT 0, 1000

-- Date: 2017-12-04 00:14
*/
INSERT INTO `scores` (`post_id`,`reviewer_id`,`rating_originality`,`rating_language`,`rating_quality`,`score`,`note`,`created`) VALUES (1,11,5,3,2,0,NULL,'2017-11-20 23:06:56');
INSERT INTO `scores` (`post_id`,`reviewer_id`,`rating_originality`,`rating_language`,`rating_quality`,`score`,`note`,`created`) VALUES (1,12,1,1,1,0,NULL,'2017-11-21 00:21:52');
INSERT INTO `scores` (`post_id`,`reviewer_id`,`rating_originality`,`rating_language`,`rating_quality`,`score`,`note`,`created`) VALUES (2,11,-1,-1,-1,0,NULL,'2017-11-21 10:50:35');
INSERT INTO `scores` (`post_id`,`reviewer_id`,`rating_originality`,`rating_language`,`rating_quality`,`score`,`note`,`created`) VALUES (2,12,5,1,3,0,NULL,'2017-11-21 11:10:23');
INSERT INTO `scores` (`post_id`,`reviewer_id`,`rating_originality`,`rating_language`,`rating_quality`,`score`,`note`,`created`) VALUES (4,11,-1,-1,-1,0,NULL,'2017-11-21 19:22:01');
INSERT INTO `scores` (`post_id`,`reviewer_id`,`rating_originality`,`rating_language`,`rating_quality`,`score`,`note`,`created`) VALUES (5,11,-1,-1,-1,0,NULL,'2017-12-03 21:09:57');
INSERT INTO `scores` (`post_id`,`reviewer_id`,`rating_originality`,`rating_language`,`rating_quality`,`score`,`note`,`created`) VALUES (5,12,5,5,4,0,NULL,'2017-12-03 21:09:58');

