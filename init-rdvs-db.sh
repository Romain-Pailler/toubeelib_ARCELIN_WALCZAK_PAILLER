#!/bin/bash
set -e

# Create databases
psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" --dbname "$POSTGRES_DB" <<-EOSQL
    CREATE DATABASE rdv;
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
