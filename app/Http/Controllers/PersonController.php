<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PeopleNatural;
use App\Models\PeopleJuridic;
use App\Models\Person;
use App\Models\Address;
use App\Models\History;
use App\Models\Payment;
use App\Models\Proceeding;
use Exception;
use Illuminate\Support\Facades\DB; // Add this line to import DB

class PersonController extends Controller
{
    protected $personModel;

    public function __construct(Person $personModel)
    {
        $this->middleware('auth');
        $this->personModel = $personModel;
    }

    protected function index(Request $request)
    {
        $proceedings = \App\Models\Proceeding::orderBy('created_at', 'DESC')
            ->with('person.juridica', 'person.persona', 'person.address')
            ->get();

        $data = $proceedings->map(function ($proceeding) {
            $person = $proceeding->person;
            $tipo_persona = null;

            $commonData = [
                'exp_id' => $proceeding->exp_id,
                'exp_numero' => $proceeding->exp_numero,
            ];

            if ($person) {
                if ($person->nat_id !== null) {
                    $personData = $person->persona;
                    $tipo_persona = 'natural';
                } elseif ($person->jur_id !== null) {
                    $personData = $person->juridica;
                    $tipo_persona = 'juridica';
                }
            }

            if ($tipo_persona === 'natural') {
                $personDataArray = [
                    'nat_dni' => $personData->nat_dni,
                    'nat_apellido_paterno' => $personData->nat_apellido_paterno,
                    'nat_apellido_materno' => $personData->nat_apellido_materno,
                    'nat_nombres' => $personData->nat_nombres,
                    'nat_telefono' => $personData->nat_telefono,
                    'nat_correo' => $personData->nat_correo,
                    'dir_calle_av' => $proceeding->person->address->dir_calle_av,
                ];
            } elseif ($tipo_persona === 'juridica') {
                $personDataArray = [
                    'jur_ruc' => $personData->jur_ruc,
                    'jur_razon_social' => $personData->jur_razon_social,
                    'jur_telefono' => $personData->jur_telefono,
                    'jur_correo' => $personData->jur_correo,
                    'dir_calle_av' => $proceeding->person->address->dir_calle_av,
                ];
            } else {
                $personDataArray = [];
            }

            return array_merge($commonData, $personDataArray, ['tipo_persona' => $tipo_persona]);
        });

        return response()->json(['data' => $data], 200);
    }

    // protected function index(Request $request)
    // {
    //     $proceedings = \App\Models\Proceeding::orderBy('created_at', 'DESC')
    //         ->with('person.address')
    //         ->get();

    //     $data = $proceedings->map(function ($proceeding) {
    //         $person = $proceeding->person;
    //         $personData = null;
    //         $type = null;
    //         if ($person) {
    //             if ($person->nat_id !== null) {
    //                 $personData = $person->persona;
    //                 $type = 'natural';
    //             } elseif ($person->jur_id !== null) {
    //                 $personData = $person->juridica;
    //                 $type = 'juridica';
    //             }
    //         }
    //         return array_merge($proceeding->toArray(), [
    //             'person_data' => $personData ? $personData->toArray() : null,
    //             'type' => $type,
    //         ]);
    //     });

    //     return response()->json(['data' => $data], 200);
    // }


    // protected function traerExpedientes(Request $request)
    // {
    //     try {
    //         DB::beginTransaction();

    //         $documento = $request->documento;
    //         $person = $this->getPersonByDocument($documento);

    //         if (!$person) {
    //             return response()->json(['state' => 1, 'message' => 'Persona no encontrada'], 404);
    //         }

    //         $expedientes = Proceeding::where('exp_demandante', $person->per_id)->get();

    //         DB::commit();

    //         return response()->json(['state' => 0, 'data' => $person, 'exps' => $expedientes], 200);
    //     } catch (Exception $e) {
    //         DB::rollback();
    //         return ['state' => '1', 'exception' => (string) $e];
    //     }
    // }

    protected function traerExpedientes(Request $request)
    {
        try {
            DB::beginTransaction();

            $documento = $request->documento;
            $person = $this->getPersonByDocument($documento);

            if (!$person) {
                return response()->json(['state' => 1, 'message' => 'Persona no encontrada'], 404);
            }

            $personaData = [];
            $tipoPersona = null;

            if ($person->natural) {
                $tipoPersona = 'natural';
                $personaData = [
                    'per_id' => $person->per_id,
                    'nat_id' => $person->natural->nat_id,
                    'nat_nombres' => $person->natural->nat_nombres,
                    'nat_apellido_paterno' => $person->natural->nat_apellido_paterno,
                    'nat_apellido_materno' => $person->natural->nat_apellido_materno,
                ];
            } elseif ($person->juridica) {
                $tipoPersona = 'juridica';
                $personaData = [
                    'per_id' => $person->per_id,
                    'jur_id' => $person->juridica->jur_id,
                    'jur_razon_social' => $person->juridica->jur_razon_social,
                ];
            }

            $expedientes = Proceeding::where('exp_demandante', $person->per_id)->get();

            DB::commit();

            $response = [
                'state' => 0,
                'persona' => $personaData,
                'tipo_persona' => $tipoPersona,
                'expedientes' => $expedientes->map(function ($expediente) {
                    return [
                        'exp_id' => $expediente->exp_id,
                        'exp_numero' => $expediente->exp_numero,
                    ];
                }),
            ];

            return response()->json($response, 200);
        } catch (Exception $e) {
            DB::rollback();
            return ['state' => '1', 'exception' => (string) $e];
        }
    }


    protected function detalleDemandante($doc)
    {
        $person = $this->getPersonByDocument($doc);

        if (!$person) {
            return response()->json(['state' => 1, 'message' => 'Persona no encontrada'], 404);
        }

        $proceedings = Proceeding::where('exp_demandante', $person->per_id)
            ->orderBy('created_at', 'DESC')
            ->get();

        $data = $proceedings->map(function ($proceeding) use ($person) {
            $tipo_persona = null;
            $commonData = [
                'exp_id' => $proceeding->exp_id,
                'exp_numero' => $proceeding->exp_numero,
            ];

            if ($person->nat_id !== null) {
                $personData = $person->persona;
                $tipo_persona = 'natural';
            } elseif ($person->jur_id !== null) {
                $personData = $person->juridica;
                $tipo_persona = 'juridica';
            }

            $address = Address::where('per_id', $person->per_id)
                ->with('district.province.departament')
                ->first();

            $personDataArray = [];
            $addressDataArray = [];

            if ($tipo_persona === 'natural') {
                $personDataArray = [
                    'nat_dni' => $personData->nat_dni,
                    'nat_apellido_paterno' => $personData->nat_apellido_paterno,
                    'nat_apellido_materno' => $personData->nat_apellido_materno,
                    'nat_nombres' => $personData->nat_nombres,
                    'nat_telefono' => $personData->nat_telefono,
                    'nat_correo' => $personData->nat_correo,
                ];
            } elseif ($tipo_persona === 'juridica') {
                $personDataArray = [
                    'jur_ruc' => $personData->jur_ruc,
                    'jur_azon_social' => $personData->jur_razon_social,
                    'jur_telefono' => $personData->jur_telefono,
                    'jur_correo' => $personData->jur_correo,
                ];
            }

            if ($address) {
                $addressDataArray = [
                    'dir_calle_av' => $address->dir_calle_av,
                    'dis_nombre' => $address->district->dis_nombre,
                    'pro_nombre' => $address->district->province->pro_nombre,
                    'dep_nombre' => $address->district->province->departament->dep_nombre,
                ];
            }

            $result = array_merge($commonData, $personDataArray, $addressDataArray, ['tipo_persona' => $tipo_persona]);
            return $result;
        });

        return response()->json(['data' => $data->first()], 200);
    }


    protected function getPersonByDocument($doc)
    {
        if (strlen($doc) === 8) {
            $persona = PeopleNatural::where('nat_dni', $doc)->first();
            return $persona ? Person::where('nat_id', $persona->nat_id)->first() : null;
        } else {
            $persona = PeopleJuridic::where('jur_ruc', $doc)->first();
            return $persona ? Person::where('jur_id', $persona->jur_id)->first() : null;
        }
    }

    protected function getHistoryByDocument($doc)
    {
        $person = $this->getPersonByDocument($doc);
        return $person ? History::where('per_id', $person->per_id)
            ->with('expediente')
            ->get() : collect([]);
    }

    protected function getPaymentsByDocument($doc)
    {
        $person = $this->getPersonByDocument($doc);
        return $person ? Payment::where('per_id', $person->per_id)
            ->with('expediente')
            ->get() : collect([]);
    }
}
