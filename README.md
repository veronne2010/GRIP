# GRIP – Gestionale Report Informazioni Pazienti

**GRIP (Gestionale Report Informazioni Pazienti)** è un software gestionale web per la gestione delle cartelle cliniche psicologiche e la produzione di report clinici strutturati.

È pensato per strutture sanitarie e socio-assistenziali, in particolare per contesti di trattamento delle tossico-dipendenze, dove è fondamentale avere dati ordinati, consultabili e facilmente stampabili.

---

## Cos’è GRIP

GRIP è un **gestionale clinico web** sviluppato in PHP, progettato per supportare psicologi e operatori sanitari nella **raccolta, gestione e consultazione delle informazioni sui pazienti**.

Il sistema consente di:

* gestire cartelle cliniche psicologiche
* organizzare informazioni sanitarie in modo strutturato
* generare report clinici stampabili
* centralizzare i dati dei pazienti in un’unica piattaforma

---

## Perché GRIP

Il nome **GRIP** è stato scelto per trasmettere affidabilità, controllo e solidità: caratteristiche fondamentali per un software che gestisce informazioni cliniche sensibili.

L’acronimo descrive chiaramente lo scopo del progetto:

* Gestionale
* Report
* Informazioni
* Pazienti

GRIP permette agli operatori di inserire, aggiornare e consultare i dati clinici dei pazienti, trasformandoli in **report chiari e facilmente consultabili**, utili sia per il lavoro quotidiano sia per la documentazione clinica.

---

## Descrizione del progetto

GRIP non nasce come prodotto enterprise, ma come progetto concreto sviluppato per rispondere a esigenze reali.

È stato realizzato **nel tempo libero**, in circa due mesi, seguendo una roadmap complessa e in continua evoluzione.

Nel corso dello sviluppo sono state esplorate diverse tecnologie:

* una prima versione in C#
* una successiva versione in Java
* una successiva versione web sviluppata in PHP
* una successiva versione in Python

L’interfaccia della versione in PHP (vd. branch 'PHP') utilizza il framework sviluppato dai team **Developers Italia** e **Designers Italia**, mantenuto sul profilo GitHub ufficiale **@italia**, garantendo coerenza con le linee guida di design della Pubblica Amministrazione.
L'interfaccia invece della versione Python è realizzata con PyQt/Pyside garantendo una interfaccia semplice e moderna

---

## Installazione

Per installare GRIP PHP su un server web Linux con Apache è sufficiente eseguire i seguenti comandi:

```bash
git clone https://github.com/veronne2010/GRIP.git
cd /var/www/html/ && rm -r * && cd ~
cd GRIP
su
cp -r * /var/www/html/
cd /var/www/html/
nano config.php
nano ente.php
```

Nei file `config.php` ed `ente.php` devono essere configurati:

* i parametri di connessione al database MariaDB o MySQL
* i dati identificativi dell’ente (obbligatori nel file `ente.php`)

Al termine della configurazione è consigliato riavviare Apache:

```bash
systemctl restart apache2
```
Al momento purtroppo non sono inclusi gli script per l'installazione del database.

---

## Contributi e proposte

Il progetto è aperto a contributi e miglioramenti.

È possibile:

* segnalare bug
* proporre nuove funzionalità
* suggerire miglioramenti tecnici o funzionali

aprendo una **Issue** su GitHub e utilizzando il tag **`Proposte`**.

---

## Stato del progetto

GRIP è un progetto attivo e in continua evoluzione.

Feedback, segnalazioni e contributi sono ben accetti per migliorare il software e ampliarne le funzionalità.
