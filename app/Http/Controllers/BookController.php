<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class BookController extends Controller
{

    public function addBook(Request $request)
    {
        try {
            // Validation
            $validator = Validator::make($request->all(), [
                'title' => 'required|string',
                'ISBN' => 'required|string|unique:books',
                // Add other validation rules for your fields
            ]);

            if ($validator->fails()) {
                return response()->json(['msg' => $validator->errors()->first()], 422);
            }

            // Create a new Book instance
            $book = new Book();
            $book->_id = Str::uuid();
            $book->title = $request->title;
            $book->author = $request->author;
            $book->genre = $request->genre;
            $book->description = $request->description;
            $book->ISBN = $request->ISBN;
            $book->year = $request->year;
            $book->counts = $request->counts;
            $book->pages = $request->pages;

            // Save the book
            $book->save();

            $response = [
                'status' => 'true',
                'msg' => 'Add Book Successfully',
                'book' => $book
            ];

            return response()->json($response, 201);
        } catch (\Exception $e) {
            // Handle exceptions
            return response()->json(['msg' => 'Error adding book: ' . $e->getMessage()], 500);
        }
    }

    public function findAll()
    {
        try {
            $books = Book::all();

            if ($books->count() > 0) {
                return response()->json([
                    'status' => true,
                    'msg' => 'All books found successfully',
                    'books' => $books
                ]);
            } else {
                return response()->json(['status' => false, 'msg' => 'No existing books']);
            }
        } catch (\Exception $e) {
            // Handle exceptions
            return response()->json(['msg' => 'Error fetching books: ' . $e->getMessage()], 500);
        }
    }
    public function findOne(Request $request)
    {
        try {
            $id = $request->id;

            if (!$id) {
                return response()->json(['status' => false, 'msg' => 'bookId is required']);
            }

            $book = Book::where('_id', $id)->first();

            if ($book) {
                return response()->json([
                    'status' => true,
                    'msg' => 'Book found successfully',
                    'book' => $book,
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'msg' => 'Book does not exist',
                    'book' => null,
                ]);
            }
        } catch (\Exception $e) {
            // Handle exceptions
            return response()->json(['msg' => 'Error finding book: ' . $e->getMessage()], 500);
        }
    }
    public function deleteBook(Request $request)
    {
        try {
            $id = $request->id;
            if (!$id) {
                return response()->json(['status' => false, 'msg' => 'bookId is required']);
            }
            $book = Book::where('_id', $id)->first();

            if ($book) {
                $book->delete();
                return response()->json(['status' => true, 'msg' => 'Book deleted successfully', 'book' => $book]);
            } else {
                return response()->json(['status' => false, 'msg' => 'Book does not exist']);
            }
        } catch (\Exception $e) {
            // Handle exceptions
            return response()->json(['msg' => 'Error deleting book: ' . $e->getMessage()], 500);
        }
    }
    public function updateBook(Request $request)
    {
        try {
            $id = $request->id;

            // Validation
            $request->validate([
                'id' => 'required',
                'title' => 'string|nullable',
                'description' => 'string|nullable',
                'author' => 'string|nullable',
                'genre' => 'string|nullable',
                'ISBN' => 'string|nullable|unique:books,ISBN,' . $id,
                'year' => 'integer|nullable',
                'counts' => 'integer|nullable',
                'pages' => 'integer|nullable',
            ]);

            // Find the book
            $book = Book::where('_id', $id)->first();

            if (!$book) {
                return response()->json(['status' => false, 'msg' => 'Book not found']);
            }

            // Update book fields
            $book->title = $request->input('title') ?? $book->title;
            $book->description = $request->input('description') ?? $book->description;
            $book->author = $request->input('author') ?? $book->author;
            $book->genre = $request->input('genre') ?? $book->genre;
            $book->ISBN = $request->input('ISBN') ?? $book->ISBN;
            $book->year = $request->input('year') ?? $book->year;
            $book->counts = $request->input('counts') ?? $book->counts;
            $book->pages = $request->input('pages') ?? $book->pages;

            // Save changes
            $book->save();

            return response()->json(['status' => true, 'msg' => 'Book updated successfully', 'book' => $book]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'msg' => 'Error updating book: ' . $e->getMessage()]);
        }
    }

    public function deleteAllBook(Request $request)
    {
        try {
            $books = Book::query()->delete();

            return response()->json(["status" => 200, "msg" => "Data Deleted Successfully", "books" => $books]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'msg' => 'Error deleting data: ' . $e->getMessage()]);
        }
    }

    //Register API POST
    // public function register(Request $request)
    // {
    //     // //Data validation

    //     $book = new Book();
    //     $book->f_name = $request->f_name;
    //     $book->l_name = $request->l_name;
    //     $book->email = $request->email;
    //     $book->location = $request->location;
    //     $book->password = Hash::make($request->password);
    //     $book->remember_token = Str::random(20);
    //     $book->save();
    //     // User::create([
    //     //     'name' => $request->name,
    //     //     'email' => $request->email,
    //     //     'password' => Hash::make($request->password),
    //     //     'remember_token' => Str::random(50),
    //     // ]);
    //     $token = $book->createToken("token")->accessToken;

    //     return response()->json([
    //         'status' => true,
    //         'msg' => 'user register Successfully',
    //         'newUser' => $book,
    //         'token' => $token,
    //     ]);
    // }
    // public function update(Request $request)
    // {
    //     // //Data validation
    //     $request->validate([
    //         'f_name' => 'required',
    //         'email' => 'required|unique:users',
    //         'password' => 'required|confirmed',
    //     ]);
    //     $book = Book::where('_id', $request->updateId)->first();
    //     $book->f_name = $request->f_name;
    //     $book->l_name = $request->l_name;
    //     $book->email = $request->email;
    //     $book->location = $request->location;
    //     $book->password = Hash::make($request->password);
    //     $book->remember_token = Str::random(20);
    //     $book->save();
    //     // User::create([
    //     //     'name' => $request->name,
    //     //     'email' => $request->email,
    //     //     'password' => Hash::make($request->password),
    //     //     'remember_token' => Str::random(50),
    //     // ]);
    //     // $token = $user->createToken("token")->accessToken;

    //     return response()->json([
    //         'status' => true,
    //         'msg' => 'user update Successfully',
    //         "updateUser" => $book
    //         // 'token' => $token,
    //     ]);
    // }

    // public function addUser(Request $request)
    // {
    //     $request->validate([
    //         'f_name' => 'required',
    //         'email' => 'required|unique:users',
    //     ]);
    //     $book = new Book();
    //     $book->f_name = $request->f_name;
    //     $book->l_name = $request->l_name;
    //     $book->email = $request->email;
    //     $book->location = $request->location;
    //     $book->remember_token = Str::random(20);
    //     $book->save();
    //     return response()->json([
    //         'status' => true,
    //         'msg' => 'user update Successfully',
    //         'addUser' => $book,
    //     ]);
    // }
    // //Profile API GET
    // //Profile Logout

    // public function destory(Request $request)
    // {
    //     $id = $request->id;
    //     $user = Book::where('_id', $request->id)->first();
    //     User::find($id)->delete();
    //     // User::find($email)->DB::delete('delete users where name = ?', ['email' => $email]);
    //     return response()->json([
    //         "user" => $user,
    //         "status" => true, "msg" => "Data Deleted Successfully",
    //     ]);
    // }
    // public function deleteAll()
    // {
    //     $user = User::all()->delete();
    //     return response()->json(["status" => 200, "msg" => "Data Deleted Successfully",]);
    // }
}
