# GRIP
Gestionale Report Informazioni Pazienti

## Cosa è GRIP
GRIP, *Gestionale Report Informazioni Pazienti*, è un nuovo modo di pensare l'aggiornamento delle Cartelle Cliniche Psicologiche per pazienti in cura in strutture per il trattamento della *tossico-dipendenza*.

## Perché GRIP
G.R.I.P. è un'acronimo studiato per infondere sicurezza nella mente di chi lo legge per via del suo significato.
Quante volte nella vita ci è capitato di sentire della *cover con il grip* ad esempio per un joystick o per il telefono, GRIP ci stampa nella mente una immagine di qualcosa che resiste e su cui affidarsi perché ci aiuta.
L'acronimo *GRIP* descrive correttamente anche le funzioni del software stesso per le sue innate caratteristiche, **è un software gestionale** da qui *Gestionale*, raccoglie informazioni sui pazienti dallo psicologo e ne permette la stampa  *Report Informazioni*, lavora con i pazienti *Pazienti*.

## Il GRIP
Insomma, questo software non sarà la quintessenza dei software simili, già solo perché è stato sviluppato a tempo perso in poco più di 2 mesi, seguendo una roadmap tediosa e complicata. Passando da una versione in C# a una in Java a quella definitiva WEB con PHP.
Inoltre, lo sviluppo di questo progetto ha sfruttato il framework sviluppato dai team Developers Italia e Designers Italia che con sinergia danno vita a [@italia](https://github.com/italia) un profilo GitHub mantenuto dagli stessi.

## Installare il GRIP

Per installare il software **GRIP** è sufficiente eseguire pochi comandi, riportati di seguito in modo completo.

```bash
git clone https://github.com/veronne2010/GRIP.git
```

```bash
cd /var/www/html/ && rm -r * && cd ~
```

```bash
cd GRIP
```

```bash
su
cp -r * /var/www/html/
```

```bash
cd /var/www/html/
```

```bash
nano config.php
nano ente.php
```

All’interno di questi file è necessario modificare i **dati di connessione al database**; in particolare, nel file `ente.php` devono essere inseriti **tutti i dati dell’ente**.

Per sicurezza, al termine della configurazione, eseguire il comando:

```bash
systemctl restart apache2
```

Questo permette di assicurarsi che il server Apache venga riavviato correttamente e che tutte le funzionalità risultino operative.


### Note sull'installazione
Tramite script sarà necessario importare tutte le tabelle MariaDB o MySQL.

Si ricorda che, a differenza di un qualunque software gestionale, questo non dispone di un'installer, per via del fatto che è stato progettato e costruito velocemente implementando le funzioni base. Sarà possibile, a mezzo ISSUE, proporre funzionalità tramite il tag *Proposte*.
