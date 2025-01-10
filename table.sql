--CREATE DATABASE praticiens_db;
--CREATE DATABASE rdv_db;
--CREATE DATABASE patients_db;
--CREATE DATABASE auth_db;
--JSP si on va utiliser les lignes au dessus

--Il faut se connecter au serveur PostgreSQL pour créer la base de données donc avec adminer rentrer les identifiants propres au Praticien / Patient / RDV 


--PostgreSQL FDW commands utils
--DROP FOREIGN TABLE IF EXISTS praticien;
--DROP FOREIGN TABLE IF EXISTS patient;
--DROP USER MAPPING IF EXISTS FOR current_user SERVER praticien_server;
--DROP USER MAPPING IF EXISTS FOR current_user SERVER patient_server;
--DROP SERVER IF EXISTS praticien_server CASCADE;
--DROP SERVER IF EXISTS patient_server CASCADE;


--Praticien DB

CREATE TABLE Praticien (
    id VARCHAR(255) PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    adresse VARCHAR(255) NOT NULL,
    tel VARCHAR(20) NOT NULL,
    specialite_id VARCHAR(255),
    FOREIGN KEY (specialite_id) REFERENCES Specialite(id)
);

CREATE TABLE Specialite (
    id VARCHAR(255) PRIMARY KEY,
    label VARCHAR(100) NOT NULL,
    description TEXT
);


--RDV DB PostgreSQL FDW

CREATE TABLE RendezVous (
    id VARCHAR(255) PRIMARY KEY,
    praticien_id VARCHAR(255) NOT NULL,
    patient_id VARCHAR(255) NOT NULL,
    specialite_id VARCHAR(255),
    statut VARCHAR(50) DEFAULT 'prévu',
    date TIMESTAMP NOT NULL,
    FOREIGN KEY (praticien_id) REFERENCES Praticien(id),
    FOREIGN KEY (patient_id) REFERENCES Patients(id),
    FOREIGN KEY (specialite_id) REFERENCES Specialite(id)
    --PB avec les foreign key 
);


--Patients DB

CREATE TABLE Patients (
    id VARCHAR(255),
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    adresse VARCHAR(255) NOT NULL,
    tel VARCHAR(20) NOT NULL,
    date_naissance DATE
);

--Users DB

CREATE TABLE Users (
    id VARCHAR(255),
    username VARCHAR(100) NOT NULL,
    password VARCHAR(100) NOT NULL,
    role VARCHAR(50) NOT NULL
);
