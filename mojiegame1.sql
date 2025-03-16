/*
Navicat MySQL Data Transfer

Source Server         : 1234
Source Server Version : 50726
Source Host           : localhost:3306
Source Database       : mojiegame1

Target Server Type    : MYSQL
Target Server Version : 50726
File Encoding         : 65001

Date: 2025-03-16 03:12:18
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for backpack_items
-- ----------------------------
DROP TABLE IF EXISTS `backpack_items`;
CREATE TABLE `backpack_items` (
  `item_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `equipment_id` int(11) NOT NULL,
  `quantity` int(11) DEFAULT '1',
  PRIMARY KEY (`item_id`),
  KEY `user_id` (`user_id`),
  KEY `equipment_id` (`equipment_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for battles
-- ----------------------------
DROP TABLE IF EXISTS `battles`;
CREATE TABLE `battles` (
  `battle_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `monster_name` varchar(50) NOT NULL,
  `damage` int(11) DEFAULT NULL,
  `result` enum('胜利','失败') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`battle_id`),
  KEY `idx_user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for default_equipment
-- ----------------------------
DROP TABLE IF EXISTS `default_equipment`;
CREATE TABLE `default_equipment` (
  `class` enum('法师','射手','战士') NOT NULL,
  `equipment_id` int(11) NOT NULL,
  PRIMARY KEY (`class`),
  KEY `equipment_id` (`equipment_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for default_skills
-- ----------------------------
DROP TABLE IF EXISTS `default_skills`;
CREATE TABLE `default_skills` (
  `class` enum('法师','射手','战士') NOT NULL,
  `skill_id` int(11) NOT NULL,
  PRIMARY KEY (`class`),
  KEY `skill_id` (`skill_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for equipment
-- ----------------------------
DROP TABLE IF EXISTS `equipment`;
CREATE TABLE `equipment` (
  `equipment_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `part` enum('头部','颈部','背部','身体','右手','左手','手指','魂魄','坐骑','箭袋') NOT NULL,
  `class` enum('法师','射手','战士') NOT NULL,
  `required_level` int(11) DEFAULT '1',
  `rarity` enum('普通','稀有','史诗','传说') DEFAULT '普通',
  `is_bound` tinyint(1) DEFAULT '0',
  `set_name` varchar(50) DEFAULT NULL,
  `description` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`equipment_id`),
  KEY `idx_name` (`name`),
  KEY `idx_rarity` (`rarity`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for equipment_attributes
-- ----------------------------
DROP TABLE IF EXISTS `equipment_attributes`;
CREATE TABLE `equipment_attributes` (
  `attribute_id` int(11) NOT NULL AUTO_INCREMENT,
  `equipment_id` int(11) DEFAULT NULL,
  `type` enum('基础属性','附魔属性','强化属性') NOT NULL,
  `name` varchar(50) NOT NULL,
  `value` int(11) NOT NULL,
  `level` int(11) DEFAULT '1',
  `description` text,
  PRIMARY KEY (`attribute_id`),
  KEY `idx_equipment_id` (`equipment_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for equipment_strengthen
-- ----------------------------
DROP TABLE IF EXISTS `equipment_strengthen`;
CREATE TABLE `equipment_strengthen` (
  `equipment_id` int(11) NOT NULL,
  `strengthen_id` int(11) NOT NULL,
  `level` int(11) DEFAULT '1',
  PRIMARY KEY (`equipment_id`,`strengthen_id`),
  KEY `strengthen_id` (`strengthen_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for gm_accounts
-- ----------------------------
DROP TABLE IF EXISTS `gm_accounts`;
CREATE TABLE `gm_accounts` (
  `gm_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `permission_level` enum('普通GM','高级GM','超级管理员') DEFAULT '普通GM',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_login_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`gm_id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `user_id` (`user_id`),
  KEY `idx_username` (`username`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for items
-- ----------------------------
DROP TABLE IF EXISTS `items`;
CREATE TABLE `items` (
  `item_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `type` enum('药水','装备','书卷','宝石','原料','道具') NOT NULL,
  `rarity` enum('普通','稀有','史诗','传说') DEFAULT '普通',
  `is_bound` tinyint(1) DEFAULT '0',
  `description` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`item_id`),
  KEY `idx_name` (`name`),
  KEY `idx_type` (`type`),
  KEY `idx_rarity` (`rarity`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for item_category_relations
-- ----------------------------
DROP TABLE IF EXISTS `item_category_relations`;
CREATE TABLE `item_category_relations` (
  `item_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  PRIMARY KEY (`item_id`,`category_id`),
  KEY `category_id` (`category_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for item_filter_relations
-- ----------------------------
DROP TABLE IF EXISTS `item_filter_relations`;
CREATE TABLE `item_filter_relations` (
  `item_id` int(11) NOT NULL,
  `filter_id` int(11) NOT NULL,
  PRIMARY KEY (`item_id`,`filter_id`),
  KEY `filter_id` (`filter_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for magic_gold_items
-- ----------------------------
DROP TABLE IF EXISTS `magic_gold_items`;
CREATE TABLE `magic_gold_items` (
  `magic_gold_item_id` int(11) NOT NULL AUTO_INCREMENT,
  `price` int(11) NOT NULL,
  `stock` int(11) DEFAULT '-1',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `item_id` int(11) NOT NULL,
  PRIMARY KEY (`magic_gold_item_id`),
  KEY `item_id` (`item_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for maps
-- ----------------------------
DROP TABLE IF EXISTS `maps`;
CREATE TABLE `maps` (
  `map_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `type` enum('城镇','荒野') NOT NULL,
  `description` text,
  `npc_info` json DEFAULT NULL,
  `monster_ids` json DEFAULT NULL,
  `adjacent_map_ids` json DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `npc_ids` json DEFAULT NULL,
  PRIMARY KEY (`map_id`),
  KEY `idx_name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for monsters
-- ----------------------------
DROP TABLE IF EXISTS `monsters`;
CREATE TABLE `monsters` (
  `monster_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `level` int(11) DEFAULT '1',
  `hp` int(11) NOT NULL,
  `attack` int(11) NOT NULL,
  `defense` int(11) NOT NULL,
  `speed` int(11) NOT NULL,
  `element` enum('火','冰','电','风') DEFAULT '火',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `equipment_ids` json DEFAULT NULL,
  `drop_item_ids` json DEFAULT NULL,
  `exp` int(11) DEFAULT '0',
  `elements` json DEFAULT NULL,
  `class` enum('法师','射手','战士') DEFAULT '战士',
  PRIMARY KEY (`monster_id`),
  KEY `idx_name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for npcs
-- ----------------------------
DROP TABLE IF EXISTS `npcs`;
CREATE TABLE `npcs` (
  `npc_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `map_id` int(11) DEFAULT NULL,
  `function_type` enum('对话','商店','任务','治疗') NOT NULL,
  `description` text,
  `class` enum('法师','射手','战士') DEFAULT '战士',
  PRIMARY KEY (`npc_id`),
  KEY `map_id` (`map_id`),
  KEY `idx_name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for npc_dialogs
-- ----------------------------
DROP TABLE IF EXISTS `npc_dialogs`;
CREATE TABLE `npc_dialogs` (
  `dialog_id` int(11) NOT NULL AUTO_INCREMENT,
  `npc_id` int(11) DEFAULT NULL,
  `dialog_text` text NOT NULL,
  `dialog_order` int(11) DEFAULT '1',
  PRIMARY KEY (`dialog_id`),
  KEY `idx_npc_id` (`npc_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for npc_shops
-- ----------------------------
DROP TABLE IF EXISTS `npc_shops`;
CREATE TABLE `npc_shops` (
  `shop_id` int(11) NOT NULL AUTO_INCREMENT,
  `npc_id` int(11) DEFAULT NULL,
  `item_id` int(11) DEFAULT NULL,
  `price` int(11) NOT NULL,
  `stock` int(11) DEFAULT '-1',
  PRIMARY KEY (`shop_id`),
  KEY `item_id` (`item_id`),
  KEY `idx_npc_id` (`npc_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for player_attributes
-- ----------------------------
DROP TABLE IF EXISTS `player_attributes`;
CREATE TABLE `player_attributes` (
  `user_id` int(11) NOT NULL,
  `level` int(11) DEFAULT '1',
  `strength` int(11) DEFAULT '0',
  `vitality` int(11) DEFAULT '0',
  `intelligence` int(11) DEFAULT '0',
  `agility` int(11) DEFAULT '0',
  `free_points` int(11) DEFAULT '0',
  PRIMARY KEY (`user_id`),
  KEY `idx_level` (`level`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for player_location
-- ----------------------------
DROP TABLE IF EXISTS `player_location`;
CREATE TABLE `player_location` (
  `user_id` int(11) NOT NULL,
  `current_map_id` int(11) NOT NULL,
  `last_map_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  KEY `current_map_id` (`current_map_id`),
  KEY `last_map_id` (`last_map_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for quests
-- ----------------------------
DROP TABLE IF EXISTS `quests`;
CREATE TABLE `quests` (
  `quest_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `description` text,
  `reward` json DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`quest_id`),
  KEY `idx_name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for shop_items
-- ----------------------------
DROP TABLE IF EXISTS `shop_items`;
CREATE TABLE `shop_items` (
  `shop_item_id` int(11) NOT NULL AUTO_INCREMENT,
  `npc_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `price` int(11) NOT NULL,
  `currency` enum('金币','代币') DEFAULT '金币',
  `stock` int(11) DEFAULT '-1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`shop_item_id`),
  KEY `idx_npc_id` (`npc_id`),
  KEY `idx_item_id` (`item_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for skills
-- ----------------------------
DROP TABLE IF EXISTS `skills`;
CREATE TABLE `skills` (
  `skill_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `skill_name` varchar(50) NOT NULL,
  `skill_level` int(11) DEFAULT '1',
  PRIMARY KEY (`skill_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for skill_points
-- ----------------------------
DROP TABLE IF EXISTS `skill_points`;
CREATE TABLE `skill_points` (
  `user_id` int(11) NOT NULL,
  `points` int(11) DEFAULT '0',
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for strengthen_attributes
-- ----------------------------
DROP TABLE IF EXISTS `strengthen_attributes`;
CREATE TABLE `strengthen_attributes` (
  `strengthen_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `effect` varchar(255) NOT NULL,
  `level` int(11) DEFAULT '1',
  `description` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`strengthen_id`),
  KEY `idx_name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `nickname` varchar(50) NOT NULL,
  `gender` enum('male','female') NOT NULL,
  `class` enum('法师','射手','战士') NOT NULL,
  `gold` int(11) DEFAULT '71',
  `backpack_capacity` int(11) DEFAULT '100',
  `warehouse_capacity` int(11) DEFAULT '200',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `current_map_id` int(11) DEFAULT '1',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `nickname` (`nickname`),
  KEY `idx_nickname` (`nickname`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for user_auth
-- ----------------------------
DROP TABLE IF EXISTS `user_auth`;
CREATE TABLE `user_auth` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `super_password_hash` varchar(255) DEFAULT NULL,
  `is_vip` tinyint(1) DEFAULT '0',
  `registered_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_login_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`),
  KEY `idx_username` (`username`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for user_currency
-- ----------------------------
DROP TABLE IF EXISTS `user_currency`;
CREATE TABLE `user_currency` (
  `user_id` int(11) NOT NULL,
  `gold` int(11) DEFAULT '0',
  `tokens` int(11) DEFAULT '0',
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for warehouse
-- ----------------------------
DROP TABLE IF EXISTS `warehouse`;
CREATE TABLE `warehouse` (
  `warehouse_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `capacity` int(11) DEFAULT '200',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`warehouse_id`),
  KEY `idx_user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for warehouse_items
-- ----------------------------
DROP TABLE IF EXISTS `warehouse_items`;
CREATE TABLE `warehouse_items` (
  `warehouse_item_id` int(11) NOT NULL AUTO_INCREMENT,
  `warehouse_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `quantity` int(11) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`warehouse_item_id`),
  KEY `idx_warehouse_id` (`warehouse_id`),
  KEY `idx_item_id` (`item_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
