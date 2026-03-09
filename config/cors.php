<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    // APPUNTI PUNTO 64 - CORS (Cross-Origin Resource Sharing) è un meccanismo di sicurezza implementato nei browser che consente o limita le richieste provenienti da origini diverse. ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    'paths' => ['api/*'],  // Specifica i percorsi per i quali le regole CORS saranno applicate. In questo caso, tutte le rotte che iniziano con 'api/' e la rotta 'sanctum/csrf-cookie' saranno soggette alle regole CORS definite in questa configurazione.

    'allowed_methods' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'], // L'asterisco '*' indica che tutte le metodologie HTTP (GET, POST, PUT, DELETE, ecc.) sono permesse. Se vuoi limitare a specifiche metodologie, puoi sostituire l'asterisco con un array di metodologie consentite, ad esempio: ['GET', 'POST'].

    'allowed_origins' => ['http://localhost:3000'], // L'aterisco '*' permette tutte le origini, ma è consigliabile specificare solo quelle necessarie per motivi di sicurezza.
     //  ma se su origins metto 'http://localhost:3000' allora solo le richieste provenienti da quella origine saranno permesse.
    
    'allowed_origins_patterns' => [],

    'allowed_headers' => ['Authorization', 'Content-Type'], // L'asterisco '*' indica che tutte le intestazioni sono permesse. Se vuoi limitare a specifiche intestazioni, puoi sostituire l'asterisco con un array di intestazioni consentite, ad esempio: ['Content-Type', 'Authorization'].
    // HEADERS sono le intestazioni HTTP che possono essere inviate con la richiesta. Se vuoi consentire solo determinate intestazioni, puoi specificarle in un array invece di usare l'asterisco '*'.

    'exposed_headers' => ['Authorization'], // Quali headers indica il server che possono essere letti dal client. Se vuoi esporre specifiche intestazioni al client, puoi elencarle in un array, ad esempio: ['X-Custom-Header'].
     //  Quando inviamo informazioni adesempio il token e vogliamo che il browser possa accedere a queste informazioni se il nostro meccanismo prevede che il token venga inviato come header personalizzato, dobbiamo specificare questo header in 'exposed_headers' per consentire al browser di accedervi.


    'max_age' => 60, // Specifica per quanto tempo (in secondi) le risposte CORS possono essere memorizzate nella cache del browser. Un valore di 0 indica che le risposte non devono essere memorizzate nella cache.

    'supports_credentials' => false,  // Indica se le richieste CORS possono includere credenziali come cookie, autorizzazioni HTTP o certificati client. Se impostato su true, le richieste CORS possono includere credenziali, ma è importante configurare correttamente 'allowed_origins' per evitare problemi di sicurezza. Se impostato su false, le richieste CORS non possono includere credenziali.


    
    // Riguardo l'autenticazione quando faccio un sistema API possono essere vari approcci, noi stiamo usando il token_base gli utenti ricevono un token che deve essere inviato in ogni richiesta successiva

];


  //FINE APPUNTI PUNTO 64 - CORS (Cross-Origin Resource Sharing) /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////