<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260211003704 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Initial e-sport schema (27 tables) SQL-first';
    }

    public function up(Schema $schema): void
    {
        $this->addSqlBatch(<<<'SQL'
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE users (
  user_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL,
  email VARCHAR(190) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('PLAYER','ORGANIZER','ADMIN') NOT NULL DEFAULT 'PLAYER',
  display_name VARCHAR(80) NOT NULL,
  bio TEXT NULL,
  phone VARCHAR(30) NULL,
  country VARCHAR(80) NULL,
  birth_date DATE NULL,
  gender ENUM('MALE','FEMALE','OTHER','UNKNOWN') NULL DEFAULT 'UNKNOWN',
  email_verified TINYINT(1) NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  last_login_at DATETIME NULL,
  profile_image_id INT UNSIGNED NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_users_username (username),
  UNIQUE KEY uq_users_email (email),
  KEY idx_users_role (role),
  KEY idx_users_profile_image_id (profile_image_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE categories (
  category_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(80) NOT NULL,
  description TEXT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_categories_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE games (
  game_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  category_id INT UNSIGNED NOT NULL,
  name VARCHAR(120) NOT NULL,
  description TEXT NULL,
  publisher VARCHAR(120) NULL,
  cover_image_id INT UNSIGNED NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_games_name (name),
  KEY idx_games_category_id (category_id),
  KEY idx_games_cover_image_id (cover_image_id),
  CONSTRAINT fk_games_category
    FOREIGN KEY (category_id) REFERENCES categories(category_id)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE images (
  image_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  file_url VARCHAR(500) NOT NULL,
  mime_type VARCHAR(60) NOT NULL,
  size_bytes BIGINT UNSIGNED NOT NULL,
  width INT UNSIGNED NULL,
  height INT UNSIGNED NULL,
  alt_text VARCHAR(255) NULL,
  uploaded_by_user_id INT UNSIGNED NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_images_uploaded_by (uploaded_by_user_id),
  CONSTRAINT fk_images_uploaded_by_user
    FOREIGN KEY (uploaded_by_user_id) REFERENCES users(user_id)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE users
  ADD CONSTRAINT fk_users_profile_image
  FOREIGN KEY (profile_image_id) REFERENCES images(image_id)
  ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE games
  ADD CONSTRAINT fk_games_cover_image
  FOREIGN KEY (cover_image_id) REFERENCES images(image_id)
  ON DELETE SET NULL ON UPDATE CASCADE;

CREATE TABLE friend_requests (
  request_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  from_user_id INT UNSIGNED NOT NULL,
  to_user_id INT UNSIGNED NOT NULL,
  status ENUM('PENDING','ACCEPTED','REFUSED','CANCELLED') NOT NULL DEFAULT 'PENDING',
  request_message VARCHAR(255) NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  responded_at DATETIME NULL,
  UNIQUE KEY uq_friend_req_pair (from_user_id, to_user_id),
  KEY idx_friend_req_to (to_user_id),
  KEY idx_friend_req_from (from_user_id),
  CONSTRAINT fk_friend_req_from
    FOREIGN KEY (from_user_id) REFERENCES users(user_id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_friend_req_to
    FOREIGN KEY (to_user_id) REFERENCES users(user_id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT chk_friend_req_not_self CHECK (from_user_id <> to_user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE friendships (
  user_id1 INT UNSIGNED NOT NULL,
  user_id2 INT UNSIGNED NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (user_id1, user_id2),
  KEY idx_friendships_user2 (user_id2),
  CONSTRAINT fk_friendships_u1
    FOREIGN KEY (user_id1) REFERENCES users(user_id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_friendships_u2
    FOREIGN KEY (user_id2) REFERENCES users(user_id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT chk_friendships_order CHECK (user_id1 < user_id2)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE posts (
  post_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  author_user_id INT UNSIGNED NOT NULL,
  content_text TEXT NULL,
  visibility ENUM('PUBLIC','FRIENDS','TEAM_ONLY') NOT NULL DEFAULT 'PUBLIC',
  is_deleted TINYINT(1) NOT NULL DEFAULT 0,
  deleted_at DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY idx_posts_author (author_user_id),
  KEY idx_posts_created (created_at),
  CONSTRAINT fk_posts_author
    FOREIGN KEY (author_user_id) REFERENCES users(user_id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE post_images (
  post_id INT UNSIGNED NOT NULL,
  image_id INT UNSIGNED NOT NULL,
  position INT UNSIGNED NOT NULL DEFAULT 1,
  PRIMARY KEY (post_id, image_id),
  KEY idx_post_images_image (image_id),
  CONSTRAINT fk_post_images_post
    FOREIGN KEY (post_id) REFERENCES posts(post_id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_post_images_image
    FOREIGN KEY (image_id) REFERENCES images(image_id)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE comments (
  comment_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  post_id INT UNSIGNED NOT NULL,
  author_user_id INT UNSIGNED NOT NULL,
  parent_comment_id INT UNSIGNED NULL,
  content_text TEXT NOT NULL,
  is_deleted TINYINT(1) NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY idx_comments_post (post_id),
  KEY idx_comments_author (author_user_id),
  KEY idx_comments_parent (parent_comment_id),
  CONSTRAINT fk_comments_post
    FOREIGN KEY (post_id) REFERENCES posts(post_id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_comments_author
    FOREIGN KEY (author_user_id) REFERENCES users(user_id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_comments_parent
    FOREIGN KEY (parent_comment_id) REFERENCES comments(comment_id)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE post_likes (
  post_id INT UNSIGNED NOT NULL,
  user_id INT UNSIGNED NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (post_id, user_id),
  KEY idx_post_likes_user (user_id),
  CONSTRAINT fk_post_likes_post
    FOREIGN KEY (post_id) REFERENCES posts(post_id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_post_likes_user
    FOREIGN KEY (user_id) REFERENCES users(user_id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE messages (
  message_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  sender_user_id INT UNSIGNED NOT NULL,
  receiver_user_id INT UNSIGNED NOT NULL,
  body_text TEXT NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  is_read TINYINT(1) NOT NULL DEFAULT 0,
  read_at DATETIME NULL,
  is_deleted_by_sender TINYINT(1) NOT NULL DEFAULT 0,
  is_deleted_by_receiver TINYINT(1) NOT NULL DEFAULT 0,
  KEY idx_messages_sender (sender_user_id),
  KEY idx_messages_receiver (receiver_user_id),
  KEY idx_messages_created (created_at),
  CONSTRAINT fk_messages_sender
    FOREIGN KEY (sender_user_id) REFERENCES users(user_id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_messages_receiver
    FOREIGN KEY (receiver_user_id) REFERENCES users(user_id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT chk_messages_not_self CHECK (sender_user_id <> receiver_user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE notifications (
  notification_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  type ENUM(
    'FRIEND_REQUEST','TEAM_INVITE','TEAM_JOIN_RESPONSE','NEW_MESSAGE',
    'TOURNAMENT_REQUEST_STATUS','ORDER_STATUS'
  ) NOT NULL,
  ref_table VARCHAR(64) NULL,
  ref_id BIGINT UNSIGNED NULL,
  content VARCHAR(255) NOT NULL,
  is_read TINYINT(1) NOT NULL DEFAULT 0,
  read_at DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_notifications_user (user_id),
  KEY idx_notifications_read (is_read, created_at),
  CONSTRAINT fk_notifications_user
    FOREIGN KEY (user_id) REFERENCES users(user_id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE reports (
  report_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  reporter_user_id INT UNSIGNED NOT NULL,
  target_type ENUM('POST','COMMENT','USER','TEAM') NOT NULL,
  target_id BIGINT UNSIGNED NOT NULL,
  reason TEXT NOT NULL,
  status ENUM('OPEN','IN_REVIEW','CLOSED') NOT NULL DEFAULT 'OPEN',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  handled_by_admin_id INT UNSIGNED NULL,
  handled_at DATETIME NULL,
  admin_note TEXT NULL,
  KEY idx_reports_reporter (reporter_user_id),
  KEY idx_reports_status (status, created_at),
  KEY idx_reports_handler (handled_by_admin_id),
  CONSTRAINT fk_reports_reporter
    FOREIGN KEY (reporter_user_id) REFERENCES users(user_id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_reports_handler
    FOREIGN KEY (handled_by_admin_id) REFERENCES users(user_id)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE teams (
  team_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  description TEXT NULL,
  region VARCHAR(80) NULL,
  logo_image_id INT UNSIGNED NULL,
  captain_user_id INT UNSIGNED NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_teams_name (name),
  KEY idx_teams_captain (captain_user_id),
  KEY idx_teams_logo (logo_image_id),
  CONSTRAINT fk_teams_captain
    FOREIGN KEY (captain_user_id) REFERENCES users(user_id)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT fk_teams_logo
    FOREIGN KEY (logo_image_id) REFERENCES images(image_id)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE team_members (
  team_id INT UNSIGNED NOT NULL,
  user_id INT UNSIGNED NOT NULL,
  joined_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  left_at DATETIME NULL,
  PRIMARY KEY (team_id, user_id),
  KEY idx_team_members_user (user_id),
  CONSTRAINT fk_team_members_team
    FOREIGN KEY (team_id) REFERENCES teams(team_id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_team_members_user
    FOREIGN KEY (user_id) REFERENCES users(user_id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE team_join_requests (
  request_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  team_id INT UNSIGNED NOT NULL,
  user_id INT UNSIGNED NOT NULL,
  status ENUM('PENDING','ACCEPTED','REFUSED','CANCELLED') NOT NULL DEFAULT 'PENDING',
  note VARCHAR(255) NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  responded_at DATETIME NULL,
  responded_by_captain_id INT UNSIGNED NULL,
  KEY idx_team_join_req_team (team_id),
  KEY idx_team_join_req_user (user_id),
  KEY idx_team_join_req_status (status),
  CONSTRAINT fk_team_join_req_team
    FOREIGN KEY (team_id) REFERENCES teams(team_id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_team_join_req_user
    FOREIGN KEY (user_id) REFERENCES users(user_id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_team_join_req_captain
    FOREIGN KEY (responded_by_captain_id) REFERENCES users(user_id)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE team_invites (
  invite_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  team_id INT UNSIGNED NOT NULL,
  invited_user_id INT UNSIGNED NOT NULL,
  invited_by_user_id INT UNSIGNED NOT NULL,
  status ENUM('PENDING','ACCEPTED','REFUSED','CANCELLED') NOT NULL DEFAULT 'PENDING',
  message VARCHAR(255) NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  responded_at DATETIME NULL,
  KEY idx_team_invites_team (team_id),
  KEY idx_team_invites_invited (invited_user_id),
  KEY idx_team_invites_by (invited_by_user_id),
  KEY idx_team_invites_status (status),
  CONSTRAINT fk_team_invites_team
    FOREIGN KEY (team_id) REFERENCES teams(team_id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_team_invites_invited_user
    FOREIGN KEY (invited_user_id) REFERENCES users(user_id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_team_invites_by_user
    FOREIGN KEY (invited_by_user_id) REFERENCES users(user_id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT chk_team_invites_not_self CHECK (invited_user_id <> invited_by_user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE products (
  product_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  team_id INT UNSIGNED NOT NULL,
  name VARCHAR(150) NOT NULL,
  description TEXT NULL,
  price DECIMAL(10,2) NOT NULL,
  stock_qty INT UNSIGNED NOT NULL DEFAULT 0,
  sku VARCHAR(64) NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_products_sku (sku),
  KEY idx_products_team (team_id),
  KEY idx_products_active (is_active),
  CONSTRAINT fk_products_team
    FOREIGN KEY (team_id) REFERENCES teams(team_id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE product_images (
  product_id INT UNSIGNED NOT NULL,
  image_id INT UNSIGNED NOT NULL,
  position INT UNSIGNED NOT NULL DEFAULT 1,
  PRIMARY KEY (product_id, image_id),
  KEY idx_product_images_image (image_id),
  CONSTRAINT fk_product_images_product
    FOREIGN KEY (product_id) REFERENCES products(product_id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_product_images_image
    FOREIGN KEY (image_id) REFERENCES images(image_id)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE carts (
  cart_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  status ENUM('OPEN','LOCKED','ORDERED') NOT NULL DEFAULT 'OPEN',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  locked_at DATETIME NULL,
  UNIQUE KEY uq_carts_user (user_id),
  CONSTRAINT fk_carts_user
    FOREIGN KEY (user_id) REFERENCES users(user_id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE cart_items (
  cart_id INT UNSIGNED NOT NULL,
  product_id INT UNSIGNED NOT NULL,
  quantity INT UNSIGNED NOT NULL DEFAULT 1,
  unit_price_at_add DECIMAL(10,2) NOT NULL,
  added_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (cart_id, product_id),
  KEY idx_cart_items_product (product_id),
  CONSTRAINT fk_cart_items_cart
    FOREIGN KEY (cart_id) REFERENCES carts(cart_id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_cart_items_product
    FOREIGN KEY (product_id) REFERENCES products(product_id)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT chk_cart_items_qty CHECK (quantity > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE orders (
  order_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_number VARCHAR(30) NOT NULL,
  cart_id INT UNSIGNED NOT NULL,
  user_id INT UNSIGNED NOT NULL,
  status ENUM('PENDING','PAID','CANCELLED','SHIPPED','DELIVERED') NOT NULL DEFAULT 'PENDING',
  payment_method ENUM('CARD','CASH','OTHER') NULL,
  payment_status ENUM('UNPAID','PAID','REFUNDED') NOT NULL DEFAULT 'UNPAID',
  total_amount DECIMAL(10,2) NOT NULL,
  shipping_address VARCHAR(255) NULL,
  phone_for_delivery VARCHAR(30) NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  paid_at DATETIME NULL,
  shipped_at DATETIME NULL,
  delivered_at DATETIME NULL,
  UNIQUE KEY uq_orders_order_number (order_number),
  UNIQUE KEY uq_orders_cart (cart_id),
  KEY idx_orders_user (user_id),
  KEY idx_orders_status (status, created_at),
  CONSTRAINT fk_orders_cart
    FOREIGN KEY (cart_id) REFERENCES carts(cart_id)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT fk_orders_user
    FOREIGN KEY (user_id) REFERENCES users(user_id)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT chk_orders_total CHECK (total_amount >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE tournament_requests (
  request_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  organizer_user_id INT UNSIGNED NOT NULL,
  game_id INT UNSIGNED NOT NULL,
  title VARCHAR(180) NOT NULL,
  description TEXT NULL,
  rules TEXT NULL,
  start_date DATE NOT NULL,
  end_date DATE NOT NULL,
  registration_deadline DATE NULL,
  max_teams INT UNSIGNED NOT NULL,
  format ENUM('BO1','BO3','BO5') NOT NULL DEFAULT 'BO1',
  registration_mode ENUM('OPEN','APPROVAL') NOT NULL DEFAULT 'OPEN',
  prize_pool DECIMAL(12,2) NOT NULL DEFAULT 0,
  prize_description VARCHAR(255) NULL,
  status ENUM('PENDING','ACCEPTED','REFUSED') NOT NULL DEFAULT 'PENDING',
  admin_response_note TEXT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  reviewed_by_admin_id INT UNSIGNED NULL,
  reviewed_at DATETIME NULL,
  KEY idx_treq_organizer (organizer_user_id),
  KEY idx_treq_game (game_id),
  KEY idx_treq_status (status, created_at),
  KEY idx_treq_reviewed_by (reviewed_by_admin_id),
  CONSTRAINT fk_treq_organizer
    FOREIGN KEY (organizer_user_id) REFERENCES users(user_id)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT fk_treq_game
    FOREIGN KEY (game_id) REFERENCES games(game_id)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT fk_treq_admin
    FOREIGN KEY (reviewed_by_admin_id) REFERENCES users(user_id)
    ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT chk_treq_dates CHECK (end_date >= start_date),
  CONSTRAINT chk_treq_max_teams CHECK (max_teams > 1),
  CONSTRAINT chk_treq_prize CHECK (prize_pool >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE tournaments (
  tournament_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  organizer_user_id INT UNSIGNED NOT NULL,
  game_id INT UNSIGNED NOT NULL,
  title VARCHAR(180) NOT NULL,
  description TEXT NULL,
  rules TEXT NULL,
  start_date DATE NOT NULL,
  end_date DATE NOT NULL,
  registration_deadline DATE NULL,
  max_teams INT UNSIGNED NOT NULL,
  format ENUM('BO1','BO3','BO5') NOT NULL DEFAULT 'BO1',
  prize_pool DECIMAL(12,2) NOT NULL DEFAULT 0,
  prize_description VARCHAR(255) NULL,
  status ENUM('DRAFT','OPEN','ONGOING','FINISHED','CANCELLED') NOT NULL DEFAULT 'DRAFT',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY idx_tournaments_organizer (organizer_user_id),
  KEY idx_tournaments_game (game_id),
  KEY idx_tournaments_status (status, start_date),
  CONSTRAINT fk_tournaments_organizer
    FOREIGN KEY (organizer_user_id) REFERENCES users(user_id)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT fk_tournaments_game
    FOREIGN KEY (game_id) REFERENCES games(game_id)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT chk_tournaments_dates CHECK (end_date >= start_date),
  CONSTRAINT chk_tournaments_max_teams CHECK (max_teams > 1),
  CONSTRAINT chk_tournaments_prize CHECK (prize_pool >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE tournament_teams (
  tournament_id INT UNSIGNED NOT NULL,
  team_id INT UNSIGNED NOT NULL,
  status ENUM('PENDING','ACCEPTED','REFUSED','CANCELLED') NOT NULL DEFAULT 'PENDING',
  seed INT UNSIGNED NULL,
  registered_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  decided_at DATETIME NULL,
  decided_by_user_id INT UNSIGNED NULL,
  checked_in TINYINT(1) NOT NULL DEFAULT 0,
  checkin_at DATETIME NULL,
  PRIMARY KEY (tournament_id, team_id),
  KEY idx_tteam_team (team_id),
  KEY idx_tteam_status (status),
  KEY idx_tteam_decider (decided_by_user_id),
  CONSTRAINT fk_tteam_tournament
    FOREIGN KEY (tournament_id) REFERENCES tournaments(tournament_id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_tteam_team
    FOREIGN KEY (team_id) REFERENCES teams(team_id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_tteam_decider
    FOREIGN KEY (decided_by_user_id) REFERENCES users(user_id)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE matches (
  match_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  tournament_id INT UNSIGNED NOT NULL,
  scheduled_at DATETIME NULL,
  round_name VARCHAR(80) NULL,
  best_of TINYINT UNSIGNED NULL,
  status ENUM('SCHEDULED','ONGOING','FINISHED','CANCELLED') NOT NULL DEFAULT 'SCHEDULED',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  result_submitted_by_user_id INT UNSIGNED NULL,
  KEY idx_matches_tournament (tournament_id),
  KEY idx_matches_status (status, scheduled_at),
  KEY idx_matches_submitted_by (result_submitted_by_user_id),
  CONSTRAINT fk_matches_tournament
    FOREIGN KEY (tournament_id) REFERENCES tournaments(tournament_id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_matches_submitted_by
    FOREIGN KEY (result_submitted_by_user_id) REFERENCES users(user_id)
    ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT chk_matches_best_of CHECK (best_of IS NULL OR best_of IN (1,3,5))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE match_teams (
  match_id INT UNSIGNED NOT NULL,
  team_id INT UNSIGNED NOT NULL,
  score INT UNSIGNED NULL,
  is_winner TINYINT(1) NULL,
  PRIMARY KEY (match_id, team_id),
  KEY idx_match_teams_team (team_id),
  CONSTRAINT fk_match_teams_match
    FOREIGN KEY (match_id) REFERENCES matches(match_id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_match_teams_team
    FOREIGN KEY (team_id) REFERENCES teams(team_id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSqlBatch(<<<'SQL'
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS match_teams;
DROP TABLE IF EXISTS matches;
DROP TABLE IF EXISTS tournament_teams;
DROP TABLE IF EXISTS tournaments;
DROP TABLE IF EXISTS tournament_requests;

DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS cart_items;
DROP TABLE IF EXISTS carts;
DROP TABLE IF EXISTS product_images;
DROP TABLE IF EXISTS products;

DROP TABLE IF EXISTS team_invites;
DROP TABLE IF EXISTS team_join_requests;
DROP TABLE IF EXISTS team_members;
DROP TABLE IF EXISTS teams;

DROP TABLE IF EXISTS reports;
DROP TABLE IF EXISTS notifications;
DROP TABLE IF EXISTS messages;
DROP TABLE IF EXISTS post_likes;
DROP TABLE IF EXISTS comments;
DROP TABLE IF EXISTS post_images;
DROP TABLE IF EXISTS posts;
DROP TABLE IF EXISTS friendships;
DROP TABLE IF EXISTS friend_requests;

DROP TABLE IF EXISTS games;
DROP TABLE IF EXISTS categories;

DROP TABLE IF EXISTS images;
DROP TABLE IF EXISTS users;

SET FOREIGN_KEY_CHECKS = 1;
SQL);
    }

    private function addSqlBatch(string $sql): void
    {
        $statements = preg_split('/;\s*(?:\R|$)/', trim($sql));

        if ($statements === false) {
            return;
        }

        foreach ($statements as $statement) {
            $statement = trim($statement);
            if ($statement !== '') {
                $this->addSql($statement);
            }
        }
    }
}
