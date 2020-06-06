/*
    Create fridgemon.sqlite3 database with the following:

    sudo sqlite3 fridgemon.sqlite3
    .read fridgemon.sql
    .exit
    mv fridgemon.sqlite3 ../db
    cd ..
    sudo chown -R www-data db/
*/


-- Table: fridge
CREATE TABLE fridge
(
    id              INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
    item            TEXT                 NOT NULL DEFAULT (''),
    expiry          DATETIME             DEFAULT NULL,
    notes           TEXT                 DEFAULT NULL,
    creator         TEXT                 NOT NULL DEFAULT (''),
    added           DATETIME             NOT NULL DEFAULT (DATETIME('NOW', 'localtime')),
    updater         TEXT                 NOT NULL DEFAULT (''),
    updated         DATETIME             DEFAULT NULL
);



-- Index: idx_item
CREATE INDEX idx_item ON fridge
(
    item COLLATE NOCASE ASC
);


-- Index: idx_expiry
CREATE INDEX idx_expiry ON fridge
(
    expiry
);
