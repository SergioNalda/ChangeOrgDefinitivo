<?php

    namespace App\Http\Controllers;

    use App\Models\Categoria;
    use App\Models\File;
    use App\Models\Peticione;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;

    class PeticioneController extends Controller
    {
        /**
         * Constructor del controlador.
         * Se aplica el middleware 'auth' a todas las acciones excepto 'index' y 'show'.
         */
        public function __construct()
        {
            $this->middleware('auth')->except(['index', 'show']); // Las acciones index y show no requieren autenticación
        }

        /**
         * Muestra una lista de peticiones públicas aceptadas, con paginación.
         */
        public function index(Request $request)
        {
            // Recuperar las peticiones y ordenarlas por fecha de creación
            $peticiones = Peticione::orderBy('created_at', 'desc')->paginate(5);

            // Retornar la vista 'peticiones.index' con las peticiones
            return view('peticiones.index', compact('peticiones'));
        }




        /**
         * Muestra los detalles de una petición específica.
         */
        public function show(Request $request, $id)
        {
            // Buscar la petición por su ID
            $peticion = Peticione::findOrFail($id);

            // Retornar la vista con la petición
            return view('peticiones.show', compact('peticion'));
        }



        /**
         * Muestra el formulario para crear una nueva petición.
         */
        public function create(Request $request)
        {
            // Aquí, puedes pasar las categorías disponibles para que el usuario elija
            $categorias = Categoria::all();

            // Cargar la vista del formulario de creación con las categorías disponibles
            return view('peticiones.edit-add', compact('categorias'));
        }


        /**
         * Almacena una nueva petición en la base de datos.
         */
        public function store(Request $request)
        {
            $this->validate($request, [
                'titulo' => 'required|max:255',
                'descripcion' => 'required',
                'destinatario' => 'required',
                'categoria'=>'required',
                'file' => 'required',
            ]);

            $input = $request->all();

            try {
                $categoria = Categoria::findOrFail($input['categoria']);
                $user = Auth::user(); //asociarlo al usuario authenticado
                $peticion = new Peticione($input);
                $peticion->categoria()->associate($categoria);
                $peticion->user()->associate($user);
                $peticion->firmantes = 0;
                $peticion->estado = 'pendiente';
                $res=$peticion->save();
                if ($res) {
                    $res_file = $this->fileUpload($request, $peticion->id);
                    if ($res_file) {
                        return redirect('/mispeticiones');
                    }
                    return back()->withError( 'Error creando la peticion')->withInput();
                }
            }catch (\Exception $exception){
                return back()->withError( $exception->getMessage())->withInput();
            }

        }

        public function fileUpload(Request $req, $peticione_id = null)
        {
            $file = $req->file('file');
            $fileModel = new File;
            $fileModel->peticione_id = $peticione_id;
            if ($req->file('file')) {
                //return $req->file('file');

                $filename = $fileName = time() . '_' . $file->getClientOriginalName();
                //      Storage::put($filename, file_get_contents($req->file('file')->getRealPath()));
                $file->move('peticiones', $filename);

                //  Storage::put($filename, file_get_contents($request->file('file')->getRealPath()));
                //   $file->move('storage/', $name);


                //$filePath = $req->file('file')->storeAs('/peticiones', $fileName, 'local');
                //    $filePath = $req->file('file')->storeAs('/peticiones', $fileName, 'local');
                // return $filePath;
                $fileModel->name = $filename;
                $fileModel->file_path = $filename;
                $res = $fileModel->save();
                return $fileModel;
                if ($res) {
                    return 0;
                } else {
                    return 1;
                }
            }
            return 1;
        }

        /**
         * Muestra las peticiones del usuario autenticado.
         */
        public function listMine()
        {
            // Obtener el ID del usuario autenticado
            $userId = Auth::id();

            // Recuperar las peticiones que fueron creadas por el usuario autenticado
            $peticiones = Peticione::where('user_id', $userId)->paginate(5);

            // Retornar la vista con las peticiones del usuario
            return view('peticiones.mine', compact('peticiones'));
        }


        /**
         * Permite al usuario firmar una petición.
         */
        public function firmar(Request $request, $id)
        {
            try {
                // Buscar la petición por su ID
                $peticion = Peticione::findOrFail($id);

                // Obtener el usuario autenticado
                $user = Auth::user();

                // Verificar si el usuario ya ha firmado la petición
                if ($peticion->firmas->contains($user)) {
                    return back()->withError("Ya has firmado esta petición")->withInput();
                }

                // Asociar al usuario con la petición
                $peticion->firmas()->attach($user);

                // Incrementar el número de firmantes
                $peticion->firmantes += 1;
                $peticion->save();

                return back()->withSuccess("Has firmado la petición exitosamente.");
            } catch (\Exception $exception) {
                return back()->withError($exception->getMessage())->withInput();
            }
        }


        public function peticionesFirmadas(Request $request)
        {
            try {
                // Obtener el usuario autenticado
                $user = Auth::user();

                // Obtener todas las peticiones firmadas por el usuario
                $peticiones = $user->firmas;  // Llama a la relación 'firmas'

                // Retornar la vista con las peticiones firmaz  das
                return view('peticiones.peticionesfirmadas', compact('peticiones'));
            } catch (\Exception $exception) {
                // Manejo de errores si algo falla
                return back()->withError($exception->getMessage())->withInput();
            }
        }




        /**
         * Muestra el formulario para editar una petición.
         */
        public function edit($id)
        {
            try {
                // Buscar la petición por ID
                $peticion = Peticione::findOrFail($id);
                $categorias = Categoria::all();  // Obtener las categorías

                // Retorna la vista con los datos de la petición y las categorías
                return view('peticiones.edit', compact('peticion', 'categorias'));
            } catch (\Exception $exception) {
                // Si la petición no se encuentra, redirige con un mensaje de error
                return back()->withError($exception->getMessage())->withInput();
            }
        }

        /**
         * Actualiza los datos de una petición en la base de datos.
         */
        public function update(Request $request, $id)
        {
            // Validar los datos recibidos del formulario
            $validated = $request->validate([
                'titulo' => 'required|max:255',
                'descripcion' => 'required',
                'destinatario' => 'required',
                'categoria' => 'required|exists:categorias,id',
            ]);

            try {
                // Buscar la petición por ID
                $peticion = Peticione::findOrFail($id);
                $peticion->update($validated);  // Actualizar los datos de la petición

                // Redirigir al usuario a la lista de sus peticiones
                return redirect()->route('peticiones.mine');
            } catch (\Exception $exception) {
                // Si ocurre un error, redirigir con el mensaje de error
                return back()->withError($exception->getMessage())->withInput();
            }
        }

        /**
         * Elimina una petición de la base de datos.
         */
        public function delete($id)
        {
            try {
                // Buscar la petición por ID y eliminarla
                $peticion = Peticione::findOrFail($id);
                $peticion->delete();

                // Redirigir al usuario a la lista de sus peticiones
                return redirect()->route('peticiones.mine');
            } catch (\Exception $exception) {
                // Si ocurre un error, redirigir con el mensaje de error
                return back()->withError($exception->getMessage())->withInput();
            }
        }
    }
