
-- ============================================
-- Table des opérateurs
-- ============================================
CREATE TABLE operateur (
    id      INTEGER PRIMARY KEY AUTOINCREMENT,
    libelle TEXT NOT NULL,          -- ex: 'Airtel'
    code    TEXT NOT NULL UNIQUE    -- ex: '033'
);

-- ============================================
-- Table des types d'operation (reference commune)
-- ============================================
CREATE TABLE type_operation (
    id  INTEGER PRIMARY KEY AUTOINCREMENT,
    nom TEXT NOT NULL UNIQUE        -- 'depot', 'retrait', 'transfert'
);

-- ============================================
-- Table des clients
-- ============================================
CREATE TABLE client (
    id            INTEGER PRIMARY KEY AUTOINCREMENT,
    numero        TEXT NOT NULL UNIQUE,      -- VARCHAR, jamais INT (garde le 0 de tete)
    nom           TEXT,
    prenom        TEXT,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    solde         REAL NOT NULL DEFAULT 0 CHECK (solde >= 0),
    id_operateur  INTEGER NOT NULL,
    FOREIGN KEY (id_operateur) REFERENCES operateur(id)
);

-- ============================================
-- Bareme de frais : par operateur ET par type d'operation
-- ============================================
CREATE TABLE bareme_frais (
    id               INTEGER PRIMARY KEY AUTOINCREMENT,
    id_operateur     INTEGER NOT NULL,
    id_type_operation INTEGER NOT NULL,
    montant_min      REAL NOT NULL,
    montant_max      REAL NOT NULL,
    frais            REAL NOT NULL,
    FOREIGN KEY (id_operateur) REFERENCES operateur(id),
    FOREIGN KEY (id_type_operation) REFERENCES type_operation(id)
);

-- ============================================
-- Historique des operations
-- ============================================
CREATE TABLE operation (
    id              INTEGER PRIMARY KEY AUTOINCREMENT,
    id_client       INTEGER NOT NULL,
    destinataire_id INTEGER,                  -- rempli seulement si transfert
    id_type_operation    INTEGER NOT NULL,
    montant         REAL NOT NULL,
    frais           REAL NOT NULL DEFAULT 0,
    date_operation  DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_client) REFERENCES client(id),
    FOREIGN KEY (destinataire_id) REFERENCES client(id),
    FOREIGN KEY (id_type_operation) REFERENCES type_operation(id)
);