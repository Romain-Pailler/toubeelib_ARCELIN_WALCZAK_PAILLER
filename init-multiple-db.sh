#!/bin/bash
set -e

# Create databases
psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" --dbname "$POSTGRES_DB" <<-EOSQL
    CREATE DATABASE patient;
    CREATE DATABASE rdv;
    CREATE DATABASE users;
EOSQL

# Insert data into each database
psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" --dbname "praticien" <<-EOSQL
    DROP TABLE IF EXISTS Praticien CASCADE;
    DROP TABLE IF EXISTS Specialite CASCADE;
    CREATE TABLE Specialite (
        id VARCHAR(255) PRIMARY KEY,
        label VARCHAR(100) NOT NULL,
        description TEXT
    );
    CREATE TABLE Praticien (
        id VARCHAR(255) PRIMARY KEY,
        nom VARCHAR(100) NOT NULL,
        prenom VARCHAR(100) NOT NULL,
        adresse VARCHAR(255) NOT NULL,
        tel VARCHAR(20) NOT NULL,
        specialite_id VARCHAR(255),
        FOREIGN KEY (specialite_id) REFERENCES Specialite(id)
    );
    INSERT INTO Specialite (id, label, description) VALUES 
        ('A','Cardiologie', 'Spécialité médicale dédiée aux troubles du cœur.'),
        ('B','Dermatologie', 'Spécialité consacrée aux maladies de la peau.'),
        ('C','Ophtalmologie', 'Spécialité des troubles de la vision.');
    INSERT INTO Praticien (id, nom, prenom, adresse, tel, specialite_id) VALUES 
        ('1','Dupont', 'Marie', '10 rue de la République, 54000 Nancy', '0601234567', 'A'),
        ('2','Martin', 'Jean', '12 avenue des Vosges, 57000 Metz', '0609876543', 'B'),
        ('3','Durand', 'Luc', '5 boulevard Saint-Michel, 75005 Paris', '0601112233', 'C');
EOSQL


# Insert data into Patients DB
psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" --dbname "patient" <<-EOSQL
    DROP TABLE IF EXISTS Patients CASCADE;
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
    DROP TABLE IF EXISTS RendezVous CASCADE;
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

# Insert data into Users DB
psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" --dbname "users" <<-EOSQL
    DROP TABLE IF EXISTS Users CASCADE;
    CREATE TABLE Users (
        id VARCHAR(255) PRIMARY KEY,
        email VARCHAR(100) NOT NULL,
        password VARCHAR(100) NOT NULL,
        role VARCHAR(50) NOT NULL
    );
EOSQL
