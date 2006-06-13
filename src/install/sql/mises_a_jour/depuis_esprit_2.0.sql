--
-- Modifications apportées à la DB depuis Esprit v2.0
--

ALTER TABLE `TypeObjetForm` DROP `DescCourteTypeObj`;

INSERT INTO `TypeObjetForm` VALUES (1, 'QTexteLong', 'Question ouverte de type « texte long »');
INSERT INTO `TypeObjetForm` VALUES (2, 'QTexteCourt', 'Question ouverte de type « texte court »');
INSERT INTO `TypeObjetForm` VALUES (3, 'QNombre', 'Question semi-ouverte de type « nombre »');
INSERT INTO `TypeObjetForm` VALUES (4, 'QListeDeroul', 'Question fermée de type « liste déroulante »');
INSERT INTO `TypeObjetForm` VALUES (5, 'QRadio', 'Question fermée de type « radio »');
INSERT INTO `TypeObjetForm` VALUES (6, 'QCocher', 'Question fermée de type « case à cocher »');
INSERT INTO `TypeObjetForm` VALUES (7, 'MPTexte', 'Elément de mise en page de type « texte »');
INSERT INTO `TypeObjetForm` VALUES (8, 'MPSeparateur', 'Elément de mise en page de type « ligne de séparation »');
