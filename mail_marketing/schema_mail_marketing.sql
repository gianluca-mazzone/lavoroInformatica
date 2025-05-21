USE if0_39043708_mail_marketing;

-- Responsabili
DROP TABLE IF EXISTS Responsabili;
CREATE TABLE Responsabili (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    cognome VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE
);

-- Clienti
DROP TABLE IF EXISTS Cliente;
CREATE TABLE Cliente (
    ID_Cliente INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100),
    cognome VARCHAR(100),
    email VARCHAR(255) NOT NULL UNIQUE,
    stato_classificazione ENUM('dormiente','pigro','curioso','interessato','entusiasta') NOT NULL DEFAULT 'dormiente',
    data_ultima_classificazione DATE NOT NULL DEFAULT CURRENT_DATE
);

-- Campagne
DROP TABLE IF EXISTS Campagna;
CREATE TABLE Campagna (
    ID_Campagna INT AUTO_INCREMENT PRIMARY KEY,
    nome_campagna VARCHAR(100) NOT NULL,
    descrizione TEXT,
    data_invio DATE NOT NULL,
    responsabile INT NOT NULL,
    numero_destinatari INT NOT NULL DEFAULT 0,
    FOREIGN KEY (responsabile) REFERENCES Responsabili(id)
);

-- Azioni
DROP TABLE IF EXISTS Azioni;
CREATE TABLE Azioni (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_cliente INT NOT NULL,
    id_campagna INT NOT NULL,
    apertura_email BOOLEAN DEFAULT 0,
    click_link BOOLEAN DEFAULT 0,
    pagine_visitate INT DEFAULT 0,
    carrello_usato BOOLEAN DEFAULT 0,
    data_azione DATE NOT NULL,
    FOREIGN KEY (id_cliente) REFERENCES Cliente(ID_Cliente) ON DELETE CASCADE,
    FOREIGN KEY (id_campagna) REFERENCES Campagna(ID_Campagna) ON DELETE CASCADE
);

-- Transizioni
DROP TABLE IF EXISTS Transizioni;
CREATE TABLE Transizioni (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_cliente INT NOT NULL,
    stato_precedente ENUM('dormiente','pigro','curioso','interessato','entusiasta') NOT NULL,
    stato_successivo ENUM('dormiente','pigro','curioso','interessato','entusiasta') NOT NULL,
    data_transizione DATE NOT NULL,
    id_campagna INT NOT NULL,
    FOREIGN KEY (id_cliente) REFERENCES Cliente(ID_Cliente) ON DELETE CASCADE,
    FOREIGN KEY (id_campagna) REFERENCES Campagna(ID_Campagna) ON DELETE CASCADE
);

-- Indici utili
CREATE INDEX idx_cliente_stato ON Cliente(stato_classificazione);
CREATE INDEX idx_transizione_cliente ON Transizioni(id_cliente);
CREATE INDEX idx_transizione_campagna ON Transizioni(id_campagna);

-- Dati di esempio
INSERT IGNORE INTO Responsabili (nome, cognome, email) VALUES
('Mario', 'Rossi', 'mario.rossi@email.it'),
('Lucia', 'Bianchi', 'lucia.bianchi@email.it');

INSERT IGNORE INTO Cliente (nome, cognome, email, stato_classificazione, data_ultima_classificazione) VALUES
('Anna', 'Verdi', 'anna.verdi@email.it', 'dormiente', '2023-06-01'),
('Paolo', 'Blu', 'paolo.blu@email.it', 'pigro', '2023-07-01');

INSERT IGNORE INTO Campagna (nome_campagna, descrizione, data_invio, responsabile, numero_destinatari) VALUES
('Promo Estate', 'Sconti su tutti i prodotti', '2024-06-01', 1, 2);

-- Query di reportistica esempio:
-- 1. Numero transizioni per tipo
SELECT stato_precedente, stato_successivo, COUNT(*) AS totale
FROM Transizioni
GROUP BY stato_precedente, stato_successivo;

-- 2. Percentuale transizioni su destinatari per campagna
SELECT c.nome_campagna, COUNT(t.id) AS transizioni, c.numero_destinatari,
       ROUND(COUNT(t.id)/c.numero_destinatari*100,1) AS percentuale
FROM Campagna c
LEFT JOIN Transizioni t ON c.ID_Campagna = t.id_campagna
GROUP BY c.ID_Campagna;
