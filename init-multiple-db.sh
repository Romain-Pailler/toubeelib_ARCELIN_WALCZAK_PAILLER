#!/bin/bash
set -e

# Create databases
psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" --dbname "$POSTGRES_DB" <<-EOSQL
    CREATE DATABASE patient;
    CREATE DATABASE rdv;
EOSQL

# Insert data into each database


# Insert data into Patients DB
psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" --dbname "patient" <<-EOSQL
    CREATE TABLE Patients (
        id VARCHAR(255) PRIMARY KEY,
        nom VARCHAR(100) NOT NULL,
        prenom VARCHAR(100) NOT NULL,
        adresse VARCHAR(255) NOT NULL,
        tel VARCHAR(20) NOT NULL,
        date_naissance DATE
    );
    INSERT INTO Patients (id, nom, prenom, adresse, tel, date_naissance) VALUES 
        ('1','Leclerc', 'Sophie', '15 rue de l Université, 75007 Paris', '0701234567', '1990-05-12'),
        ('2','Moreau', 'Pierre', '8 avenue des Champs-Élysées, 75008 Paris', '0709876543', '1985-11-22'),
        ('3','Bernard', 'Claire', '20 rue Lafayette, 54000 Nancy', '0712345678', '1995-03-14');
EOSQL

# Insert data into RDV DB
psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" --dbname "rdv" <<-EOSQL
    CREATE TABLE RendezVous (
        id VARCHAR(255) PRIMARY KEY,
        praticien_id VARCHAR(255) NOT NULL,
        patient_id VARCHAR(255) NOT NULL,
        specialite_id VARCHAR(255),
        statut VARCHAR(50) DEFAULT 'prévu',
        date TIMESTAMP NOT NULL
    );
    INSERT INTO RendezVous (id,praticien_id, patient_id, specialite_id, statut, date) VALUES 
        ('1','1', '1', 'A', 'confirmé', '2024-11-20 09:00:00'),
        ('2','2', '2', 'B', 'prévu', '2024-11-21 10:30:00'),
        ('3','3', '3', 'C', 'annulé', '2024-11-22 14:00:00');
EOSQL
