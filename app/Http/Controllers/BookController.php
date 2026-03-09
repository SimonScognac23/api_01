<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class BookController extends Controller
{
    public function index()
    {
        $books = Book::all(); // verifichiamo solo se il libro esiste, non se l'utente è autorizzato a vederlo
        return response()->json([
            'data' => $books,
            'links' => [
                'self' => [
                    'href' => url('/api/books'),
                    'method' => 'GET'
                ],
                'create' => [
                    'href' => url('/api/books'),
                    'method' => 'POST'
                ]
            ]
        ]);
    }

    public function show($id)
    {
        if (!$book = Book::find($id)) {
            return response()->json(['error' => 'Book not found'], 404);
        }

        // SECURE
        // $user = $book->user()->first();

        // $userFiltered['name'] = $user->name;
        // $userFiltered['email'] = $user->email;

        return response()->json([
            'data' => $book,
            //'user' => $userFiltered,
            'links' => [
                'self' => [
                    'href' => url("/api/books/{$id}"),
                    'method' => 'GET'
                ],
                'update' => [
                    'href' => url("/api/books/{$id}"),
                    'method' => 'PUT'
                ],
                'delete' => [
                    'href' => url("/api/books/{$id}"),
                    'method' => 'DELETE'
                ],
                'all_books' => [
                    'href' => url('/api/books'),
                    'method' => 'GET'
                ]
            ]
        ]);
    }

    public function store(Request $request)
    {
        // UNSECURE
        // Missing Validation
        $book = Book::create($request->all());

        return response()->json([
            'data' => $book,
            'links' => [
                'self' => [
                    'href' => url("/api/books/{$book->id}"),
                    'method' => 'GET'
                ],
                'update' => [
                    'href' => url("/api/books/{$book->id}"),
                    'method' => 'PUT'
                ],
                'delete' => [
                    'href' => url("/api/books/{$book->id}"),
                    'method' => 'DELETE'
                ],
                'all_books' => [
                    'href' => url('/api/books'),
                    'method' => 'GET'
                ]
            ]
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $book = Book::find($id);

        if (!$book) {
            return response()->json(['error' => 'Book not found'], 404);
        }

        // SECURE  // Verifichiamo se l'utente è autenticato e se è il proprietario del libro prima di consentire l'aggiornamento
        // if(!$user = Auth::user()){
        //     return response()->json(['error' => 'Not autorised'], 401);
        // }
        // if($user->id != $book->user_id){
        //     return response()->json(['error' => 'Not autorised'], 401);
        // }

        // UNSECURE
        // Missing Validation
        // Missing Authorization Check

        // APPUNTI PUNTO 66 /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        // Verifichiamo se l'utente è autenticato
        if ($book->user_id !== auth()->id()) { // Verifichiamo se l'utente autenticato è il proprietario del libro
            return response()->json(['error' => 'Not authorized'], 403); // Restituiamo un errore 403 Forbidden se l'utente non è autorizzato a modificare il libro
        } // Se l'utente è autorizzato, procediamo con l'aggiornamento del libro

        // FINE APPUNTI PUNTO 66//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


        $book->update($request->all());

        return response()->json([
            'data' => $book,
            'links' => [
                'self' => [
                    'href' => url("/api/books/{$id}"),
                    'method' => 'GET'
                ],
                'delete' => [
                    'href' => url("/api/books/{$id}"),
                    'method' => 'DELETE'
                ],
                'all_books' => [
                    'href' => url('/api/books'),
                    'method' => 'GET'
                ]
            ]
        ]);
    }

    public function destroy($id)
    {
        $book = Book::find($id); // verifichiamo solo se il libro esiste, non se l'utente è autorizzato a cancellarlo

        if (!$book) { // Stiamo verificando solo se il libro esiste, non se l'utente è autorizzato a cancellarlo
            return response()->json(['error' => 'Book not found'], 404);
        }
        // SECURE // Verifichiamo se l'utente è autenticato e se è il proprietario del libro prima di consentire la cancellazione
        // if(!$user = Auth::user()){
        //     return response()->json(['error' => 'Not autorised'], 401);
        // }
        // if($user->id != $book->user_id){
        //     return response()->json(['error' => 'Not autorised'], 401);
        // }

        // APPUNTI PUNTO 66 /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        // Verifichiamo se l'utente è autenticato

        if ($book->user_id !== auth()->id()) { // Verifichiamo se l'utente autenticato è il proprietario del libro
            return response()->json(['error' => 'Not authorized'], 403); // Restituiamo un errore 403 Forbidden se l'utente non è autorizzato a cancellare il libro
        } // Se l'utente è autorizzato, procediamo con la cancellazione del libro

        // FINE APPUNTI PUNTO 66//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////7

        // UNSECURE
        $book->delete();

        return response()->json([
            'message' => 'Book deleted successfully',
            'links' => [
                'all_books' => [
                    'href' => url('/api/books'),
                    'method' => 'GET'
                ],
                'create' => [
                    'href' => url('/api/books'),
                    'method' => 'POST'
                ]
            ]
        ]);
    }
}
