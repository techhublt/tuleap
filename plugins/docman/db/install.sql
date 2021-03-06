DROP TABLE IF EXISTS plugin_docman_item;
CREATE TABLE plugin_docman_item (
  item_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  parent_id INT(11) UNSIGNED NULL,
  group_id INT(11) UNSIGNED NULL,
  title TEXT NULL,
  description TEXT NULL,
  create_date INT(11) UNSIGNED NULL,
  update_date INT(11) UNSIGNED NULL,
  delete_date INT(11) UNSIGNED NULL,
  user_id INT(11) UNSIGNED NULL,
  status TINYINT(4) DEFAULT 100 NOT NULL,
  obsolescence_date int(11) DEFAULT 0 NOT NULL,
  rank INT(11) DEFAULT 0 NOT NULL,
  item_type INT(11) UNSIGNED NULL,
  link_url TEXT NULL,
  wiki_page TEXT NULL,
  file_is_embedded INT(11) UNSIGNED NULL,
  PRIMARY KEY(item_id),
  KEY idx_group_id (group_id),
  KEY parent_id (parent_id),
  KEY rank (rank),
  KEY search (group_id, delete_date, obsolescence_date),
  FULLTEXT fltxt_title (title),
  FULLTEXT fltxt_description (description),
  FULLTEXT fltxt (title, description)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS plugin_docman_item_deleted;
CREATE TABLE plugin_docman_item_deleted (
  item_id INT(11) UNSIGNED NOT NULL,
  parent_id INT(11) UNSIGNED NULL,
  group_id INT(11) UNSIGNED NULL,
  title TEXT NULL,
  description TEXT NULL,
  create_date INT(11) UNSIGNED NULL,
  update_date INT(11) UNSIGNED NULL,
  delete_date INT(11) UNSIGNED NULL,
  purge_date INT(11) UNSIGNED NULL,
  user_id INT(11) UNSIGNED NULL,
  status TINYINT(4) DEFAULT 100 NOT NULL,
  obsolescence_date int(11) DEFAULT 0 NOT NULL,
  rank INT(11) DEFAULT 0 NOT NULL,
  item_type INT(11) UNSIGNED NULL,
  link_url TEXT NULL,
  wiki_page TEXT NULL,
  file_is_embedded INT(11) UNSIGNED NULL,
  PRIMARY KEY(item_id)
);

DROP TABLE IF EXISTS plugin_docman_version;
CREATE TABLE plugin_docman_version (
  id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  item_id INT(11) UNSIGNED NULL,
  number INT(11) UNSIGNED NULL,
  user_id INT(11) UNSIGNED NULL,
  label TEXT NULL,
  changelog TEXT NULL,
  date INT(11) UNSIGNED NULL,
  filename TEXT NULL,
  filesize INT(11) UNSIGNED NULL,
  filetype TEXT NULL,
  path TEXT NULL,
  PRIMARY KEY(id),
  KEY item_id (item_id),
  FULLTEXT fltxt (label, changelog, filename)
) ENGINE=MyISAM;


CREATE TABLE plugin_docman_version_deleted (
  id INT(11) UNSIGNED NOT NULL,
  item_id INT(11) UNSIGNED NULL,
  number INT(11) UNSIGNED NULL,
  user_id INT(11) UNSIGNED NULL,
  label TEXT NULL,
  changelog TEXT NULL,
  create_date INT(11) UNSIGNED NULL,
  delete_date INT(11) UNSIGNED NULL,
  purge_date INT(11) UNSIGNED NULL,
  filename TEXT NULL,
  filesize INT(11) UNSIGNED NULL,
  filetype TEXT NULL,
  path TEXT NULL,
  PRIMARY KEY(id),
  KEY item_id (item_id)
);

DROP TABLE IF EXISTS plugin_docman_log;
CREATE TABLE plugin_docman_log (
  time      INT(11) UNSIGNED NOT NULL default '0',
  group_id  INT(11)          NOT NULL default '0',
  item_id   INT(11) UNSIGNED NOT NULL default '0',
  user_id   INT(11)          NOT NULL default '0',
  type      TINYINT(4)       NOT NULL default '0',
  old_value TEXT,
  new_value TEXT,
  field TEXT DEFAULT NULL,
  KEY time (time),
  KEY item_id (item_id),
  KEY group_id (group_id)
);

DROP TABLE IF EXISTS plugin_docman_project_settings;
CREATE TABLE plugin_docman_project_settings (
  group_id  INT(11)          NOT NULL default '0',
  view      VARCHAR(255),
  use_obsolescence_date TINYINT(4) DEFAULT 0 NOT NULL,
  use_status TINYINT(4) DEFAULT 0 NOT NULL,
  KEY group_id (group_id)
);

DROP TABLE IF EXISTS plugin_docman_tokens;
CREATE TABLE plugin_docman_tokens (
   user_id INT(11) NOT NULL,
   token CHAR(32) NOT NULL,
   url text NOT NULL default '',
   created_at DATETIME NOT NULL,
   PRIMARY KEY(user_id, token)
);

--
-- Table structure for table 'plugin_docman_metadata'
--
--
-- field_id      : id of the metadata
-- group_id      : id of the group where the metadata is
-- name          : the name of the field (must be indentical to the
--                 column name in the artifact table
-- data_type     : type of the value of this field
--                 TEXT = 1 - DATE = 4 - USER = 5 - STRING = 6
--
-- label         : short name (used on the HTML form)
-- description   : longer description of this field
-- required      : 0 a project can decide not to use this artifact field
--                 1 all projects have to use this artifact field
-- empty_ok      : 0 this field must always be assigned a value
--                 1 empty value (null) is ok
-- special       : 0 process this field as usual
--                 1 this field require some special processing
-- default_value : default value for the metadata
-- use_it        : 0 metadata not used
--                 1 metadata used
--
--
DROP TABLE IF EXISTS plugin_docman_metadata;
CREATE TABLE plugin_docman_metadata (
  field_id int(11) NOT NULL auto_increment,
  group_id int(11) NOT NULL,
  name varchar(255) NOT NULL default '',
  data_type int(11) NOT NULL default '0',
  label varchar(255) NOT NULL default '',
  description text NOT NULL,
  required int(11) NOT NULL default '0',
  empty_ok int(11) NOT NULL default '0',
  mul_val_ok TINYINT(4)  NOT NULL default '0',
  special int(11) NOT NULL default '0',
  default_value text NOT NULL default '',
  use_it TINYINT(4)  NOT NULL default '0',
  PRIMARY KEY  (field_id),
  KEY idx_name (name (10)),
  KEY idx_group_id (group_id)
);

--
-- Table structure for table 'plugin_docman_metadata_value'
--
-- Store values of metadata for an item. It may have multiple values for a
-- given item (should only happend with valueInt, because it correspond to
-- multiple values selection in a select box).
--
-- field_id    : id of the metadata (FK plugin_docman_metadata.field_id)
-- item_id     : id of the item (FK plugin_docman_item.item_id)
-- valueInt    : id of the listofelement value
--               (FK plugin_docman_metadata_love.value_id)
-- valueText   : A text value
-- valueDate   : A date value (stored as an int: date based on EPOCH)
-- valueString : A string value
--
--
DROP TABLE IF EXISTS plugin_docman_metadata_value;
CREATE TABLE plugin_docman_metadata_value (
  field_id int(11) NOT NULL,
  item_id int(11) NOT NULL,
  valueInt INT(11) NULL,
  valueText text,
  valueDate int(11),
  valueString text,
  KEY idx_field_item_id (field_id, item_id),
  FULLTEXT fltxt (valueText, valueString),
  FULLTEXT fltxt_txt (valueText),
  FULLTEXT fltxt_str (valueString)
) ENGINE=MyISAM;

--
-- Table structure for table 'plugin_docman_metadata_love'
-- 
-- 'love' stands for ListOfValuesElement
--
-- value_id        : the id of the value
-- name            : the text value
-- description     : An explanation of the value (not used a lot but...)
-- rank            : number telling at which place in the select box
--                   a value must appear
-- status          : A the value is active. It displays in select boxes
--                   H the value is hidden (not shown in select boxes but
--                   it is still here for old artifacts using it
--                   P the value is permanent. It means that it is active and
--                   it cannot be changed to hidden by the project even if 
--                   artifact field has a 'project' scope (very useful to force
--                   some commonly accepted values to appear in the select
--                   box. The 'None' values are good examples of that)
--
--
DROP TABLE IF EXISTS plugin_docman_metadata_love;
CREATE TABLE plugin_docman_metadata_love (
  value_id int(11) NOT NULL auto_increment,
  name text NOT NULL,
  description text NOT NULL,
  rank int(11) NOT NULL default '0',
  status char(1) NOT NULL default 'A',
  PRIMARY KEY  (value_id),
  KEY rank (rank),
  KEY name (name (10)),
  KEY idx_fv_status (status)
);


--
-- Table structure for table 'plugin_docman_metadata_love_md'
--
-- 'love' stands for ListOfValuesElement and 'md' for MetaData
--
-- Make the link between 'plugin_docman_listofvalues' and
-- 'plugin_docman_metadata' tables
--
-- field_id        : the id of the field (FK plugin_docman_metadata.field_id)
-- value_id        : the id of the value (in plugin_docman_metadata_love.value_id)
--
--
DROP TABLE IF EXISTS plugin_docman_metadata_love_md;
CREATE TABLE plugin_docman_metadata_love_md (
  field_id int(11) NOT NULL default '0',
  value_id int(11) NOT NULL default '0',
  PRIMARY KEY  (field_id, value_id)
);

--
-- Table structure for table 'plugin_docman_approval'
--
-- table_id    Id of the table
-- item_id     Id of the item (FK plugin_docman_item (item_id))
-- version_id  Id of the item version (FK plugin_docman_version (id))
-- wiki_version_Id Id of the wiki page version (FK wiki_version(version))
-- table_owner User who creates the table (FK user (user_id)) 
-- date        Table creation date 
-- description A text that describe why the approval is required.
-- status      Table activation state: 0 - Disabled / 1 - Enabled / 2 - Closed
-- notification Type of notification: 0 - Disabled / 1 - Once at all / 2 - Sequential
-- auto_status  Does the table automaticaly change document status (0 - false / 1 true)
--
DROP TABLE IF EXISTS plugin_docman_approval;
CREATE TABLE plugin_docman_approval (
  table_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  item_id INT(11) UNSIGNED NULL DEFAULT NULL,
  version_id INT(11) UNSIGNED NULL DEFAULT NULL,
  wiki_version_id INT(11) UNSIGNED NULL DEFAULT NULL,
  table_owner INT(11) UNSIGNED NOT NULL,
  date INT(11) UNSIGNED NULL,
  description TEXT NULL,
  status TINYINT(4) DEFAULT 0 NOT NULL,
  notification TINYINT(4) DEFAULT 0 NOT NULL,
  notification_occurence INT(11) DEFAULT 0,
  auto_status TINYINT(4) DEFAULT 0 NOT NULL,
  PRIMARY KEY(table_id),
  UNIQUE KEY version_id (version_id),
  UNIQUE KEY item_id(item_id,wiki_version_id)
);

--
-- Table structure for table 'plugin_docman_approval_user'
--
-- table_id    Id of the table the reviewer belong to (FK plugin_docman_approval (table_id))
-- reviewer_id Id of user member of the table (FK user (user_id))
-- date        Date of the decision.
-- state       State of the review: 0 - Not Yet / 1 - Approved / 2 - Rejected
-- comment     A text to comment the state.
-- version     The version of the document on approval
--
DROP TABLE IF EXISTS plugin_docman_approval_user;
CREATE TABLE plugin_docman_approval_user (
  table_id INT(11) UNSIGNED NOT NULL,
  reviewer_id INT(11) UNSIGNED NOT NULL,
  rank INT(11) DEFAULT 0 NOT NULL,
  date INT(11) UNSIGNED NULL,
  state TINYINT(4) DEFAULT 0 NOT NULL,
  comment TEXT NULL,
  version INT(11) UNSIGNED NULL,
  PRIMARY KEY(table_id, reviewer_id),
  INDEX rank (rank),
  INDEX idx_reviewer (reviewer_id, table_id)
);

--
-- Table structure for table 'plugin_docman_report'
--
-- report_id         
-- name             Name of the search (listed in 'Search' select box)
-- title            Title of the report (if set, replace the default 
--                  Prjname - Project Doc)
-- group_id         (FK groups.group_id)
-- user_id          Owner of the report (FK user.user_id)
-- item_id          On witch item (folder) the report applies.
-- scope            Scope of the report ('I' individual, 'P' project)
-- is_default       Is the default report (not in use).
-- advanced_search  Is this search 'Advanced' (for dates and list of values).
-- description      Text that describe the report.
-- image            Add an image (FK plugin_docman_item.item_id)
DROP TABLE IF EXISTS plugin_docman_report;
CREATE TABLE plugin_docman_report (
  report_id       int(11) NOT NULL auto_increment,
  name            varchar(255) NULL,
  title           varchar(255) NULL,
  group_id        int(11) NOT NULL,
  user_id         int(11) NOT NULL DEFAULT 100,
  item_id         int(11) NULL,
  scope           char(1) NOT NULL default 'I',
  is_default      tinyint(1) NOT NULL default 0,
  advanced_search tinyint(1) NOT NULL default 0,
  description     text NULL,
  image           int(11)NULL, 
  PRIMARY KEY (report_id),
  INDEX group_idx (group_id),
  INDEX user_idx (user_id)
);

--
-- Table structure for table 'plugin_docman_report_metadata'
--
DROP TABLE IF EXISTS plugin_docman_report_filter;
CREATE TABLE plugin_docman_report_filter (
  report_id     INT(11) NOT NULL,
  label         VARCHAR(255) NOT NULL,
  value_love    INT(11) NULL,
  value_string  VARCHAR(255) NULL,
  value_date1   VARCHAR(32) NULL,
  value_date2   VARCHAR(32) NULL,
  value_date_op tinyint(2) NULL,
  INDEX report_label_idx(report_id, label(10))
);

DROP TABLE IF EXISTS plugin_docman_widget_embedded;
CREATE TABLE plugin_docman_widget_embedded(
    id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    owner_id int(11) unsigned NOT NULL,
    owner_type varchar(1) NOT NULL,
    title varchar(255) NOT NULL,
    item_id INT(11) UNSIGNED NOT NULL,
    KEY (owner_id, owner_type)
);

--
-- Table structure for table 'plugin_docman_item_lock'
-- Host informations about locked items
--
DROP TABLE IF EXISTS plugin_docman_item_lock;
CREATE TABLE plugin_docman_item_lock (
  item_id INT(11) UNSIGNED NOT NULL DEFAULT 0,
  user_id INT(11) NOT NULL DEFAULT 0,
  lock_date INT(11) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (item_id)
);

-- Enable service for project 1 and 100
INSERT INTO service(group_id, label, description, short_name, link, is_active, is_used, scope, rank) VALUES ( 100 , 'plugin_docman:service_lbl_key' , 'plugin_docman:service_desc_key' , 'docman', '/plugins/docman/?group_id=$group_id', 1 , 1 , 'system',  95 );
INSERT INTO service(group_id, label, description, short_name, link, is_active, is_used, scope, rank) VALUES ( 1   , 'plugin_docman:service_lbl_key' , 'plugin_docman:service_desc_key' , 'docman', '/plugins/docman/?group_id=1', 1 , 1 , 'system',  95 );

-- Create service for all other projects (but disabled)
INSERT INTO service(group_id, label, description, short_name, link, is_active, is_used, scope, rank)
SELECT DISTINCT group_id , 'plugin_docman:service_lbl_key' , 'plugin_docman:service_desc_key' , 'docman', CONCAT('/plugins/docman/?group_id=', group_id), 1 , 0 , 'system',  95
FROM service
WHERE group_id NOT IN (SELECT group_id
    FROM service
    WHERE short_name
    LIKE 'docman');


--
-- Create document references
--

-- First, remove existing reference to legacy docman.
-- It was almost never used, and since we keep the same keywords, we can't keep both
DELETE FROM reference WHERE id='10';
DELETE FROM reference WHERE id='11';
DELETE FROM reference WHERE id='17';
DELETE FROM reference WHERE id='18';

-- Create new references
INSERT INTO reference SET 
    id='10',        
    keyword='doc', 
    description='reference_doc_desc_key', 
    link='/plugins/docman/?group_id=$group_id&action=show&id=$1', 
    scope='S', 
    service_short_name='docman', 
    nature='document';

INSERT INTO reference SET 
    id='11',        
    keyword='document', 
    description='reference_doc_desc_key', 
    link='/plugins/docman/?group_id=$group_id&action=show&id=$1', 
    scope='S', 
    service_short_name='docman', 
    nature='document';

INSERT INTO reference SET 
    id='17',        
    keyword='folder', 
    description='reference_doc_desc_key', 
    link='/plugins/docman/?group_id=$group_id&action=show&id=$1', 
    scope='S', 
    service_short_name='docman', 
    nature='document';

INSERT INTO reference SET 
    id='18',        
    keyword='dossier', 
    description='reference_doc_desc_key', 
    link='/plugins/docman/?group_id=$group_id&action=show&id=$1', 
    scope='S', 
    service_short_name='docman', 
    nature='document';

-- Enable document references for project 1 and 100
DELETE FROM reference_group WHERE reference_id='10' AND group_id='100';
DELETE FROM reference_group WHERE reference_id='11' AND group_id='100';
DELETE FROM reference_group WHERE reference_id='17' AND group_id='100';
DELETE FROM reference_group WHERE reference_id='18' AND group_id='100';
DELETE FROM reference_group WHERE reference_id='10' AND group_id='1';
DELETE FROM reference_group WHERE reference_id='11' AND group_id='1';
DELETE FROM reference_group WHERE reference_id='17' AND group_id='1';
DELETE FROM reference_group WHERE reference_id='18' AND group_id='1';
INSERT INTO reference_group SET reference_id='10', group_id='100', is_active='1';
INSERT INTO reference_group SET reference_id='11', group_id='100', is_active='1';
INSERT INTO reference_group SET reference_id='17', group_id='100', is_active='1';
INSERT INTO reference_group SET reference_id='18', group_id='100', is_active='1';
INSERT INTO reference_group SET reference_id='10', group_id='1', is_active='1';
INSERT INTO reference_group SET reference_id='11', group_id='1', is_active='1';
INSERT INTO reference_group SET reference_id='17', group_id='1', is_active='1';
INSERT INTO reference_group SET reference_id='18', group_id='1', is_active='1';


                              
INSERT INTO permissions_values VALUES ('PLUGIN_DOCMAN_READ', 1, 0);
INSERT INTO permissions_values VALUES ('PLUGIN_DOCMAN_READ', 2, 1);
INSERT INTO permissions_values VALUES ('PLUGIN_DOCMAN_READ', 3, 0);
INSERT INTO permissions_values VALUES ('PLUGIN_DOCMAN_READ', 4, 0);
INSERT INTO permissions_values VALUES ('PLUGIN_DOCMAN_WRITE', 1, 0);
INSERT INTO permissions_values VALUES ('PLUGIN_DOCMAN_WRITE', 2, 0);
INSERT INTO permissions_values VALUES ('PLUGIN_DOCMAN_WRITE', 3, 1);
INSERT INTO permissions_values VALUES ('PLUGIN_DOCMAN_WRITE', 4, 0);
INSERT INTO permissions_values VALUES ('PLUGIN_DOCMAN_MANAGE', 1, 0);
INSERT INTO permissions_values VALUES ('PLUGIN_DOCMAN_MANAGE', 2, 0);
INSERT INTO permissions_values VALUES ('PLUGIN_DOCMAN_MANAGE', 3, 0);
INSERT INTO permissions_values VALUES ('PLUGIN_DOCMAN_MANAGE', 4, 1);
INSERT INTO permissions_values VALUES ('PLUGIN_DOCMAN_ADMIN', 1, 0);
INSERT INTO permissions_values VALUES ('PLUGIN_DOCMAN_ADMIN', 2, 0);
INSERT INTO permissions_values VALUES ('PLUGIN_DOCMAN_ADMIN', 3, 0);
INSERT INTO permissions_values VALUES ('PLUGIN_DOCMAN_ADMIN', 4, 1);

INSERT INTO plugin_docman_metadata_love(value_id, name, description, rank, status) VALUES (100, 'love_special_none_name_key', 'love_special_none_desc_key', 0, 'P');

-- Install site documentation
INSERT INTO plugin_docman_item (item_id, parent_id, group_id, title, description, create_date, update_date, delete_date, user_id, status, obsolescence_date, rank, item_type, link_url, wiki_page, file_is_embedded) VALUES (1, 0, 1, 'roottitle_lbl_key', '', UNIX_TIMESTAMP(NOW()), UNIX_TIMESTAMP(NOW()), NULL, 101, 0, 0, 0, 1, NULL, NULL, NULL);

INSERT INTO plugin_docman_item (item_id, parent_id, group_id, title, description, create_date, update_date, delete_date, user_id, status, obsolescence_date, rank, item_type, link_url, wiki_page, file_is_embedded) VALUES (2, 1, 1, 'English Documentation', '', UNIX_TIMESTAMP(NOW()), UNIX_TIMESTAMP(NOW()), NULL, 101, 0, 0, 0, 1, NULL, NULL, NULL);

INSERT INTO plugin_docman_item (item_id, parent_id, group_id, title, description, create_date, update_date, delete_date, user_id, status, obsolescence_date, rank, item_type, link_url, wiki_page, file_is_embedded) VALUES (3, 2, 1, 'User Guide', 'A comprehensive guide describing all the services and how to use them in an optimal way. Also provides a lot of useful tips and guidelines to manage your project efficiently.', UNIX_TIMESTAMP(NOW()), UNIX_TIMESTAMP(NOW()), NULL, 101, 0, 0, 0, 1, NULL, NULL, NULL);
INSERT INTO plugin_docman_item (item_id, parent_id, group_id, title, description, create_date, update_date, delete_date, user_id, status, obsolescence_date, rank, item_type, link_url, wiki_page, file_is_embedded) VALUES (4, 3, 1, 'Multi-page HTML Version', '', UNIX_TIMESTAMP(NOW()), UNIX_TIMESTAMP(NOW()), NULL, 101, 0, 0, 1, 3, '/documentation/user_guide/html/en_US/index.html', '', NULL);
INSERT INTO plugin_docman_item (item_id, parent_id, group_id, title, description, create_date, update_date, delete_date, user_id, status, obsolescence_date, rank, item_type, link_url, wiki_page, file_is_embedded) VALUES (5, 3, 1, 'Single-page HTML (2.7 MB) Version', '', UNIX_TIMESTAMP(NOW()), UNIX_TIMESTAMP(NOW()), NULL, 101, 0, 0, 2, 3, '/documentation/user_guide/html/en_US/User_Guide.html', '', NULL);

INSERT INTO plugin_docman_item (item_id, parent_id, group_id, title, description, create_date, update_date, delete_date, user_id, status, obsolescence_date, rank, item_type, link_url, wiki_page, file_is_embedded) VALUES (6, 2, 1, 'Command-Line Interface', 'A comprehensive guide describing all the functions of the Command-Line Interface.', UNIX_TIMESTAMP(NOW()), UNIX_TIMESTAMP(NOW()), NULL, 101, 0, 0, 1, 1, NULL, NULL, NULL);
INSERT INTO plugin_docman_item (item_id, parent_id, group_id, title, description, create_date, update_date, delete_date, user_id, status, obsolescence_date, rank, item_type, link_url, wiki_page, file_is_embedded) VALUES (7, 6, 1, 'Multi-page HTML Version', '', UNIX_TIMESTAMP(NOW()), UNIX_TIMESTAMP(NOW()), NULL, 101, 0, 0, -2, 3, '/documentation/cli/html/en_US/index.html', '', NULL);
INSERT INTO plugin_docman_item (item_id, parent_id, group_id, title, description, create_date, update_date, delete_date, user_id, status, obsolescence_date, rank, item_type, link_url, wiki_page, file_is_embedded) VALUES (8, 6, 1, 'Single-page HTML Version', '', UNIX_TIMESTAMP(NOW()), UNIX_TIMESTAMP(NOW()), NULL, 101, 0, 0, 0, 3, '/documentation/cli/html/en_US/CLI.html', '', NULL);

INSERT INTO plugin_docman_item (item_id, parent_id, group_id, title, description, create_date, update_date, delete_date, user_id, status, obsolescence_date, rank, item_type, link_url, wiki_page, file_is_embedded) VALUES (9, 1, 1, 'Documentation en français', '', UNIX_TIMESTAMP(NOW()), UNIX_TIMESTAMP(NOW()), NULL, 101, 0, 0, 1, 1, NULL, NULL, NULL);

INSERT INTO plugin_docman_item (item_id, parent_id, group_id, title, description, create_date, update_date, delete_date, user_id, status, obsolescence_date, rank, item_type, link_url, wiki_page, file_is_embedded) VALUES (10, 9, 1, 'Guide de l\'Utilisateur', 'Un guide complet décrivant tous les services et comment les utiliser de manière optimale. Fournit également de nombreuses astuces et explications pour gérer efficacement votre projet.', UNIX_TIMESTAMP(NOW()), UNIX_TIMESTAMP(NOW()), NULL, 101, 0, 0, 0, 1, NULL, NULL, NULL);
INSERT INTO plugin_docman_item (item_id, parent_id, group_id, title, description, create_date, update_date, delete_date, user_id, status, obsolescence_date, rank, item_type, link_url, wiki_page, file_is_embedded) VALUES (11, 10, 1, 'Version HTML multi-pages', '', UNIX_TIMESTAMP(NOW()), UNIX_TIMESTAMP(NOW()), NULL, 101, 0, 0, 1, 3, '/documentation/user_guide/html/fr_FR/index.html', '', NULL);
INSERT INTO plugin_docman_item (item_id, parent_id, group_id, title, description, create_date, update_date, delete_date, user_id, status, obsolescence_date, rank, item_type, link_url, wiki_page, file_is_embedded) VALUES (12, 10, 1, 'Version HTML une page (4,2 Mo)', '', UNIX_TIMESTAMP(NOW()), UNIX_TIMESTAMP(NOW()), NULL, 101, 0, 0, 2, 3, '/documentation/user_guide/html/fr_FR/User_Guide.html', '', NULL);

INSERT INTO plugin_docman_item (item_id, parent_id, group_id, title, description, create_date, update_date, delete_date, user_id, status, obsolescence_date, rank, item_type, link_url, wiki_page, file_is_embedded) VALUES (13, 9, 1, 'Interface de Commande en Ligne', 'Un guide complet décrivant toutes les fonctions de l\'Interface de Commande en Ligne.', UNIX_TIMESTAMP(NOW()), UNIX_TIMESTAMP(NOW()), NULL, 101, 0, 0, 1, 1, NULL, NULL, NULL);
INSERT INTO plugin_docman_item (item_id, parent_id, group_id, title, description, create_date, update_date, delete_date, user_id, status, obsolescence_date, rank, item_type, link_url, wiki_page, file_is_embedded) VALUES (14, 13, 1, 'Version HTML multi-pages', '', UNIX_TIMESTAMP(NOW()), UNIX_TIMESTAMP(NOW()), NULL, 101, 0, 0, 4, 3, '/documentation/cli/html/fr_FR/index.html', '', NULL);
INSERT INTO plugin_docman_item (item_id, parent_id, group_id, title, description, create_date, update_date, delete_date, user_id, status, obsolescence_date, rank, item_type, link_url, wiki_page, file_is_embedded) VALUES (15, 13, 1, 'Version HTML une page', '', UNIX_TIMESTAMP(NOW()), UNIX_TIMESTAMP(NOW()), NULL, 101, 0, 0, 5, 3, '/documentation/cli/html/fr_FR/CLI.html', '', NULL);

INSERT INTO permissions(permission_type, ugroup_id, object_id) 
SELECT 'PLUGIN_DOCMAN_READ', 1, item_id
FROM plugin_docman_item;

INSERT INTO permissions(permission_type, ugroup_id, object_id) 
SELECT 'PLUGIN_DOCMAN_MANAGE', 3, item_id
FROM plugin_docman_item;

-- Instanciate docman in default template project
INSERT INTO plugin_docman_item (parent_id, group_id, title, description, create_date, update_date, delete_date, user_id, status, obsolescence_date, rank, item_type, link_url, wiki_page, file_is_embedded) VALUES (0, 100, 'roottitle_lbl_key', '', UNIX_TIMESTAMP(NOW()), UNIX_TIMESTAMP(NOW()), NULL, 101, 0, 0, 0, 1, NULL, NULL, NULL);

INSERT INTO  plugin_docman_project_settings (group_id, view, use_obsolescence_date, use_status) 
VALUES (100, 'Tree', 0, 0);

INSERT INTO permissions(permission_type, ugroup_id, object_id) 
SELECT 'PLUGIN_DOCMAN_READ', 2, item_id
FROM plugin_docman_item
WHERE group_id = 100;

INSERT INTO permissions(permission_type, ugroup_id, object_id) 
SELECT 'PLUGIN_DOCMAN_WRITE', 3, item_id
FROM plugin_docman_item
WHERE group_id = 100;

INSERT INTO permissions(permission_type, ugroup_id, object_id) 
SELECT 'PLUGIN_DOCMAN_MANAGE', 4, item_id
FROM plugin_docman_item
WHERE group_id = 100;

INSERT INTO permissions(permission_type, ugroup_id, object_id) 
VALUES ('PLUGIN_DOCMAN_ADMIN', 4, 100);

