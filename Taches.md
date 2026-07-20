# Suivi des tâches - Projet Mobile Money

**Sujet** : Examen Projet Final - S4 Info et Design - Juillet 2026
**Binôme** :
- Côté opérateur : RAKOTONJANAHARY Hajaniaina Olivier
- Côté client : RAFALIMANANA Tsiory Fandresena

**Stack** : PHP (CodeIgniter 4), SQLite embarqué, HTML/CSS/JS, Bootstrap

---

## Version 1 (Tag : v1)

### Base de données (commun)

- [ok] Schéma `operateur`, `client`, `type_operation`, `bareme_frais`, `operation`
- [ok] Contrainte solde >= 0 sur `client`
- [ok] Fichier `base.sql` à la racine (tables + données)
- [ok] Migrations CodeIgniter correspondantes
- [ok] Seeder `InitialSeeder` (données de base)

### RAKOTONJANAHARY Hajaniaina Olivier — Côté opérateur

- [ok] Config préfixes opérateur (033, 037) - table `operateur`
- [ok] CRUD types d'opération (dépôt, retrait, transfert)
- [ok] CRUD barème de frais par tranche (modifiable, par opérateur + type)
- [ok] Fonction de calcul du frais selon montant + opérateur + type
- [ok] Écran gains par type de frais (retrait / transfert)
- [ok] Écran situation des comptes clients (liste + soldes)

### RAFALIMANANA Tsiory Fandresena — Côté client

- [ok] Route + vue login (formulaire numéro de téléphone)
    -[ok] Creation page login dans /client avec comme champ numero de tel seulement 
- [ok] Contrôleur login : vérifier préfixe valide
    - [ok] erreur si prefixe invalide 
- [ok] Contrôleur login : créer client auto si numéro inconnu (avec solde 0)
- [ok] Session client connecté
- [ok] Écran solde
- [ok] Formulaire + traitement dépôt
- [ok] Formulaire + traitement retrait (vérifier solde suffisant)
- [ok] Formulaire + traitement transfert (vérifier solde suffisant + destinataire existe)
- [ok] Écran historique des opérations du client

---


## Version 2 (Tag : v2)

### RAKOTONJANAHARY Hajaniaina Olivier — Côté opérateur

- [] Config préfixes valables pour les autres opérateurs (ex: 032, 031...) - étendre table `operateur`
- [] Config commission (%) sur transferts vers un autre opérateur (nouvelle colonne ou table dédiée, ex: `commission_inter_operateur`)
- [] Fonction de calcul de la commission selon opérateur émetteur + opérateur destinataire
- [] Page "Gains" : séparer visuellement/statistiquement mon opérateur vs les autres opérateurs
- [] Écran "Montants à envoyer à chaque opérateur" (agrégation des sommes dues entre opérateurs suite aux transferts inter-opérateurs)

### RAFALIMANANA Tsiory Fandresena — Côté client
- [ok] modif: si essaie de creer un client de type autre operateur -> la bloquer
  - [ok] sinon , on la cree si de notre operateur
- [] Formulaire transfert : checkbox "Inclure les frais de retrait du destinataire"
  - [] Calculer les frais de retrait qui s'appliqueraient au destinataire
  - [] Ajouter ce montant au débit de l'expéditeur (montant + frais transfert + frais retrait destinataire)
  - [] Créditer le destinataire du montant net voulu + frais de retrait anticipés
- [] Formulaire "Envoi multiple" : plusieurs numéros + un montant total
  - [] Diviser le montant total en parts égales selon le nombre de destinataires
  - [] Calculer les frais de transfert par part (pas sur le total) selon barème/tranche
  - [] Vérifier solde suffisant pour couvrir toutes les parts + tous les frais
  - [] Vérifier que chaque numéro destinataire existe (ou créer si logique cohérente avec v1)
  - [] Enregistrer une opération par destinataire dans l'historique
