<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse {

    $validator = Validator::make($request->all(), [
        'name' => 'required',
        'email' => 'required|email',
        // SECURE
        //'password' => 'required|regex:/^(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_\-=+])[A-Za-z\d!@#$%^&*()_\-=+]{8,20}$/',  
        'password' => 'required',
        
        'c_password' => 'required|same:password',
    ]);
    
    if($validator->fails()){  
        return response()->json(['error' =>  $validator->errors()], 403);    
    }
    
    $input = $request->all();
    
    $input['password'] = bcrypt($input['password']);
    $user = User::create($input);
    
    $success['token'] =  $user->createToken('MyApp')->plainTextToken;
    $success['name'] =  $user->name;
    
    return response()->json([
        'data' => $success,
        'links' => [
            'self' => [
                'href' => url('/api/register'),
                'method' => 'POST'
            ],
            'all_books' => [
                'href' => url('/api/books'),
                'method' => 'GET'
                ]
            ]
        ]);
    }

    public function login(Request $request): JsonResponse {
        
    if(!Auth::attempt(['email' => $request->email, 'password' => $request->password])){ 
        return response()->json(['error' =>  ['Unauthorised']], 403);    
    }
    
    $user = Auth::user(); 

    // $user->tokens()->delete(); // decidere se eliminare i vecchi token
    // $success['token'] = $user->createToken('MyApp', ['*'], now()->addDays(7))->plainTextToken; // Scade tra 7 giorni (esempio)

    // Gli utenti ricevono un token che deve essere inviato in ogni richiesta successiva. Il token non scade, ma può essere revocato manualmente (es. logout) o automaticamente (es. dopo un certo periodo di inattività).
    // Quando sta facendo una request da localStorage o sessionStorage, il token puo essere salvato con una scadenza (es. 1 ora) e rinnovato automaticamente quando sta per scadere, ma questo richiede una logica aggiuntiva nel client.
    // Cookie httponly non è consigliato per i token di autenticazione, perché non permette al client di gestire il token (es. rinnovarlo), ma è più sicuro contro gli attacchi XSS. Se si usa un cookie httponly, è importante implementare una logica di rinnovo del token e di gestione della sessione sul server.
    // In questo esempio, il token non scade, ma può essere revocato manualmente (es. logout) o automaticamente (es. dopo un certo periodo di inattività). Se si vuole implementare una scadenza, è necessario aggiungere una logica per gestire la scadenza dei token e il rinnovo automatico.
    $success['token'] =  $user->createToken('MyApp')->plainTextToken; // Success ha token senza scadenza
    $success['name'] =  $user->name;
    

    // Risposta con token e link alle risorse (HATEOAS)
    // Il token puo essere salvato con dei cookie httpOnly facendo attenzione a non esporlo a JS, oppure puo essere salvato in localStorage o sessionStorage (ma attenzione a XSS). In ogni caso, il token deve essere inviato nell'header Authorization

    return response()->json([
        'data' => $success, // In token stampiamo solo il token, non l'user
        'links' => [
            'self' => [
                'href' => url('/api/login'), // link alla risorsa di login
                'method' => 'POST'
            ],
            'all_books' => [
                'href' => url('/api/books'),
                'method' => 'GET'
                ]
            ]
        ]);
    }
                            
    // UNSECURE
    // ONLY A DEMO, NOT WORKING
    // API4:2023 Unrestricted Resource Consumption
    public function passwordRecovery(Request $request){
        if(!$user = User::where('email',$request->email)->first()){
            return response()->json(['error' =>  ['Unauthorised']]);
        }


        // APPUNTI PUNTO 67 ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////77////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        // Invia un SMS con un codice di conferma al numero di telefono dell'utente. Il codice deve essere univoco e temporaneo, e deve essere associato all'utente in modo sicuro (es. hashato). L'utente deve inserire il codice ricevuto per confermare la sua identità e procedere con il recupero della password. Questo metodo è più sicuro del semplice invio di un link via email, perché richiede l'accesso al telefono dell'utente, ma è importante implementare misure di sicurezza per prevenire abusi (es. limitare il numero di tentativi, verificare il numero di telefono, ecc.).
        // Use sms api to send confirmation code to user number
        // Se non ce nessun limitazione, un attaccante potrebbe inviare un numero illimitato di SMS a un numero di telefono, causando costi elevati per l'utente o per il servizio. Inoltre, se il codice di conferma è prevedibile o non sufficientemente complesso, un attaccante potrebbe indovinarlo e compromettere l'account dell'utente. Per prevenire questi problemi, è importante implementare limitazioni sul numero di tentativi e assicurarsi che i codici di conferma siano univoci e difficili da indovinare.
        // $newCode = SMS::generateCode();
        // $user->smsCode = $newCode;
        // $user->save();
        // SMS::send($user->phone, ['Please don't share this code: $user->smsCode']);
        
        // FINE APPUNTI PUNTO 67/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////77///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        return response()->json([
            'data' => 'SMS sent to $user->phone',
            'links' => [
                'self' => [
                    'href' => url('/api/login'),
                    'method' => 'POST'
                ],
                'all_books' => [
                    'href' => url('/api/books'),
                    'method' => 'GET'
                    ]
                    ]
                ]);
    }
                                    
    function getUserInfo($id) {
        
        // SECURE (manual)
        // $token = $request->bearerToken();
        // // 5|mUEqxncaO2zLLKtCSlLQoGeoxkS46FkygBItGRdAd7a0ab93
        // $parts = explode('|', $token);
        // // array:2 [ // app/Http/Controllers/AuthController.php:115
        // //   0 => "5"
        // //   1 => "mUEqxncaO2zLLKtCSlLQoGeoxkS46FkygBItGRdAd7a0ab93"
        // // ]
        // $hashedToken = hash('sha256', $parts[1]);
        // // Cerca il token nel database
        // $tokenRecord = PersonalAccessToken::where('token', $hashedToken)->first();
        // // Recuper l'user
        // $user = User::find($tokenRecord->tokenable_id);
        
        // if(!$user){
        //     return response()->json(['error' =>  ['Unauthorised']]);
        // }
        
        // SECURE (con Auth)
        // if(!$user = Auth::user()){
        //     return response()->json(['error' =>  ['Unauthorised']]);
        // }

        // UNSECURE
        if(!$user = User::find($id)){
            return response()->json(['error' =>  ['User not found']]);
        };

        return response()->json([
            'data' => $user,
            'links' => [
                'self' => [
                    'href' => url('/api/user'),
                    'method' => 'GET'
                ],
                'all_books' => [
                    'href' => url('/api/books'),
                    'method' => 'GET'
                    ]
                ]
            ]);
        }
                                            
    public function updateEmail(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error.',
                'errors' => $validator->errors()
            ], 422);
        }

        // APPUNTI PUNTO 66 ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////77
        
        // SECURE
        $user = Auth::user();
        

        // UNSECURE
        // $user = User::findOrFail($request->user_id); // Qui stiamo permettendo a chiunque di aggiornare l'email di qualsiasi utente, basta conoscere l'user_id. Questo è un grave problema di sicurezza, perché permette a un attaccante di compromettere gli account degli utenti semplicemente indovinando o ottenendo gli user_id.
        // Io devo permettere la modifica solo al proprietario dell'account, quindi devo verificare che l'utente autenticato sia lo stesso dell'user_id che sta cercando di modificare. In questo modo, anche se un attaccante conosce gli user_id, non potrà modificare le email degli altri utenti, perché non sarà autenticato come loro.


        $user->email = $request->email; // Qui stiamo aggiornando l'email dell'utente autenticato, quindi è sicuro, perché un utente può modificare solo la propria email. Tuttavia, è importante assicurarsi che l'email sia unica e valida, altrimenti potremmo avere problemi di integrità dei dati o di comunicazione con gli utenti.
        $user->save(); // Salviamo le modifiche al database. 
        
        // FINE APPUNTI PUNTO 66/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        return response()->json([
            'success' => true,
            'message' => 'Email updated successfully.',
            'links' => [
                'all_books' => [
                    'href' => url('/api/books'),
                    'method' => 'GET'
                    ]
                    ]
                ]);
    }
}
                                                