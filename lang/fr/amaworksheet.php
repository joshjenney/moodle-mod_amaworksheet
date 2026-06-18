<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * French strings for Amanote.
 *
 * @package     mod_amaworksheet
 * @copyright   2020 Amaplex Software
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Amanote Worksheet';
$string['modulename'] = 'Fichier d\exercices';
$string['modulenameplural'] = 'Fichiers d\exercices';
$string['modulename_help'] = 'Un fichier d\'exercices est un PDF contenant des questions ou des exercices pour les élèves. Le plugin Worksheet d\'Amanote permet aux élèves de répondre aux questions directement sur ou à côté du PDF et aux enseignants de récupérer les réponses des élèves.';
$string['amaworksheet:addinstance'] = 'Ajouter fichier d\'exercices';
$string['amaworksheet:view'] = 'Ouvrir le fichier d\'exercices';
$string['amaworksheetcontent'] = 'Fichiers et sous-dossiers';
$string['downloadfile'] = 'Télécharger';
$string['clicktodownloadfile'] = 'Télécharger le fichier PDF';
$string['clicktoamaworksheet'] = 'Ouvrir le fichier d\exercices';
$string['openstudentsnotes'] = 'Ouvrir les réponses des étudiants';
$string['openstudentsnotes_help'] = 'Cela vous permet d\'ouvrir les réponses que les étudiants vous ont envoyées.';
$string['podcastcreatorbutton'] = 'Ajouter des explications orales';
$string['openpodcastcreator'] = 'Ajouter des explications orales';
$string['openpodcastcreator_help'] = 'Ajouter des explications orales sur votre fichier d\'exercices.';
$string['submissionnotification'] = '{$a} a soumis un travail';
$string['key'] = 'Clé d\'activation';
$string['key_help'] = 'Cette clé est requise pour les fonctionalités avancées telles que le créateur de podcasts.';
$string['dnduploadamaworksheet'] = 'Ajouter un fichier d\'exercices';
$string['showdate'] = 'Afficher la date de dépôt/de modification';
$string['showdate_desc'] = 'Afficher la date de dépôt/de modification sur la page de cours?';
$string['showdate_help'] = 'Permet d\'afficher la date de dépôt ou de modification à côté du lien vers la ressource.';
$string['showsize'] = 'Afficher la taille';
$string['showsize_desc'] = 'Si ce réglage est activé, la date de dépôt/de modification est affichée sur la page du cours';
$string['showsize_help'] = 'Permet d\'afficher la taille, par example \'3.1 MB\', à côté du lien vers la ressource.';
$string['printintro'] = 'Afficher la description de la ressource';
$string['printintroexplain'] = 'Affiche la description de la resource au-dessus du contenu.';
$string['uploadeddate'] = 'Déposé le {$a}';
$string['modifieddate'] = 'Modifié le {$a}';
$string['amaworksheetdetails_sizetype'] = '{$a->size} {$a->type}';
$string['amaworksheetdetails_sizedate'] = '{$a->size} {$a->date}';
$string['amaworksheetdetails_typedate'] = '{$a->type} {$a->date}';
$string['amaworksheetdetails_sizetypedate'] = '{$a->size} {$a->type} {$a->date}';
$string['openinamaworksheet'] = 'Ouvrir le fichier d\'exercices';
$string['openinamaworksheet_help'] = 'Ouvrir le document dans Amanote permet de démarrer ou continuer une prise de note.';
$string['cannotcreatetoken'] = 'Ouvrir le fichier d\'exercices';
$string['cannotcreatetoken_help'] = 'Vous n\'avez pas les permissions pour ouvrir ce document dans Amanote.';
$string['servicenotavailable'] = 'Ouvrir le fichier d\'exercices';
$string['servicenotavailable_help'] = 'Le service n\'est pas activé. Veuillez contacter l\'administrateur du site.';
$string['guestsarenotallowed'] = 'Ouvrir le fichier d\'exercices';
$string['guestsarenotallowed_help'] = 'Les visiteurs anonymes ne peuvent pas ouvrir de ressources dans Amanote. Veuillez vous connecter pour accéder à cette fonctionnalité.';
$string['unexpectederror'] = 'Ouvrir le fichier d\'exercices';
$string['unexpectederror_help'] = 'Une erreur inattendue est survenue, la ressource n\'est pas ouvrable dans Amanote. Veuillez contacter l\'administrateur du site.';
$string['unsecureconnection'] = 'Attention ! Votre connexion n\'est pas sécurisée.';
$string['privacy:metadata'] = 'Pour s\'intégrer avec Amanote, certaines données utilisateur doivent être envoyées à l\'application Amanote (système distant).';
$string['privacy:metadata:userid'] = 'Le userid est envoyé depuis Moodle pour accélérer le processus d\'authentification.';
$string['privacy:metadata:fullname'] = 'Le nom complet de l\'utilisateur est envoyé au système distant pour permettre une meilleure expérience utilisateur.';
$string['privacy:metadata:email'] = 'L\'adresse e-mail de l\'utilisateur est envoyée au système distant pour permettre une meilleure expérience utilisateur (partage de notes, notification, etc.).';
$string['privacy:metadata:subsystem:corefiles'] = 'Les fichiers PDF sont stockés avec le système de fichiers Moodle.';
$string['pluginadministration'] = 'Administration du module Amanote';
$string['privacy:metadata:access_token'] = 'Le jeton d\'accès est nécessaire pour sauvegarder les notes dans l\'espace privé Moodle de l\'utilisateur.';
$string['privacy:metadata:access_token_expiration'] = 'La date d\'expiration du jeton d\'accès est envoyée pour empêcher un utilisateur d\'utiliser l\'application avec un jeton expiré.';
$string['messageprovider:submission'] = 'Soumission d\'un travail d\'un étudiant';
$string['importantinformationheading'] = 'Important installation information';
$string['importantinformationdescription'] = 'Afin que le module fonctionne correctement, veuillez vérifier que les exigences suivantes sont respectées:

1. Les services web sont activés (Administration du site > Fonctions avancées)

2. Le service *Moodle mobile web service* est activé (Administration du site > Plugins > Web services > Services externes)

3. Le protocole REST est activé (Administration du site > Plugins > Services web > Gérer les protocoles)

4. La capacité *webservice/rest:use* est autorisée pour les *utilisateurs authentifiés* (Administration du site > Utilisateurs > Permissions > Définition des rôles > Utilisateur authentifié > Gérer les rôles)';
