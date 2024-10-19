#!/bin/bash
set -e

# Create databases
psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" --dbname "$POSTGRES_DB" <<-EOSQL
    CREATE DATABASE patient;
    CREATE DATABASE praticien;
    CREATE DATABASE rdv;
EOSQL

# Insert data into each database

# Insert data into Praticien DB
psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" --dbname "praticien" <<-EOSQL
    CREATE TABLE Specialite (
        id SERIAL PRIMARY KEY,
        label VARCHAR(100) NOT NULL,
        description TEXT
    );
    CREATE TABLE Praticien (
        id SERIAL PRIMARY KEY,
        nom VARCHAR(100) NOT NULL,
        prenom VARCHAR(100) NOT NULL,
        adresse VARCHAR(255) NOT NULL,
        tel VARCHAR(20) NOT NULL,
        specialite_id INTEGER,
        FOREIGN KEY (specialite_id) REFERENCES Specialite(id)
    );
    INSERT INTO Specialite (label, description) VALUES 
        ('Cardiologie', 'Spécialité médicale dédiée aux troubles du cœur.'),
        ('Dermatologie', 'Spécialité consacrée aux maladies de la peau.'),
        ('Ophtalmologie', 'Spécialité des troubles de la vision.');
    INSERT INTO Praticien (nom, prenom, adresse, tel, specialite_id) VALUES 
        ('Dupont', 'Marie', '10 rue de la République, 54000 Nancy', '0601234567', 1),
        ('Martin', 'Jean', '12 avenue des Vosges, 57000 Metz', '0609876543', 2),
        ('Durand', 'Luc', '5 boulevard Saint-Michel, 75005 Paris', '0601112233', 3);
EOSQL

# Insert data into Patients DB
psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" --dbname "patient" <<-EOSQL
    CREATE TABLE Patients (
        id SERIAL PRIMARY KEY,
        nom VARCHAR(100) NOT NULL,
        prenom VARCHAR(100) NOT NULL,
        adresse VARCHAR(255) NOT NULL,
        tel VARCHAR(20) NOT NULL,
        date_naissance DATE
    );
    INSERT INTO Patients (nom, prenom, adresse, tel, date_naissance) VALUES 
        ('Leclerc', 'Sophie', '15 rue de l Université, 75007 Paris', '0701234567', '1990-05-12'),
        ('Moreau', 'Pierre', '8 avenue des Champs-Élysées, 75008 Paris', '0709876543', '1985-11-22'),
        ('Bernard', 'Claire', '20 rue Lafayette, 54000 Nancy', '0712345678', '1995-03-14');
EOSQL

# Insert data into RDV DB
psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" --dbname "rdv" <<-EOSQL
    CREATE TABLE RendezVous (
        id SERIAL PRIMARY KEY,
        praticien_id INTEGER NOT NULL,
        patient_id INTEGER NOT NULL,
        specialite_id INTEGER,
        statut VARCHAR(50) DEFAULT 'prévu',
        date TIMESTAMP NOT NULL
    );
    INSERT INTO RendezVous (praticien_id, patient_id, specialite_id, statut, date) VALUES 
        (1, 1, 1, 'confirmé', '2024-11-20 09:00:00'),
        (2, 2, 2, 'prévu', '2024-11-21 10:30:00'),
        (3, 3, 3, 'annulé', '2024-11-22 14:00:00');
EOSQL
