<!DOCTYPE html>
<html>
<head>
    <title>Laravel AJAX Formations Management</title>
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/css/iziToast.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/js/iziToast.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script> -->
    <!-- Material Icons -->
    <link href="{{ asset('assets/css/material-icons.css') }}" rel="stylesheet">
    <!-- iziToast CSS -->
    <link href="{{ asset('assets/css/iziToast.min.css') }}" rel="stylesheet">
    <!-- jQuery -->
    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery-3.5.1.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery-3.6.0.min.js') }}"></script>
    <!-- Popper.js -->
    <script src="{{ asset('assets/js/popper.min.js') }}"></script>
    <!-- Bootstrap Bundle JS -->
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <!-- SweetAlert2 -->
    <script src="{{ asset('assets/js/sweetalert2.min.js') }}"></script>
    <!-- iziToast JS -->
    <script src="{{ asset('assets/js/iziToast.min.js') }}"></script>



    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        .imgUpload {
            max-width: 90px;
            max-height: 70px;
            min-width: 50px;
            min-height: 50px;
        }
        .required::after {
            content: " *";
            color: red;
        }
        .form-control {
            border: 1px solid #ccc;
        }
        .form-control:focus {
            border-color: #66afe9;
            outline: 0;
            box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 8px rgba(102, 175, 233, 0.6);
        }
    </style>
</head>
<body>

<!-- Add Student Modal -->
<div class="modal fade" id="etudiantAddModal" tabindex="-1" aria-labelledby="etudiantAddModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="etudiantAddModalLabel">Ajouter un étudiant à la formation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="new-student-session_id">
                <div class="form-group">
                    <label for="student-phone-search" class="form-label">Numéro de téléphone de l'étudiant:</label>
                    <input type="text" class="form-control" id="student-phone-search" placeholder="Entrez le numéro de téléphone">
                </div>
                <button type="button" class="btn btn-primary" onclick="searchStudentByPhone()">Rechercher</button>
                <div id="student-search-results"></div>
                <div id="payment-form" style="display:none;">
                    <div class="row mb-3">
                        <div class="form-group col-md-4">
                            <label for="formation-price" class="form-label">Prix Programme:</label>
                            <input type="text" class="form-control" id="formation-price" readonly>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="prix-reel" class="form-label">Prix Réel:</label>
                            <input type="text" class="form-control" id="prix-reel" placeholder="Entrez le prix réel">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="montant-paye" class="form-label">Montant Payé:</label>
                            <input type="text" class="form-control" id="montant-paye" placeholder="Entrez le montant payé">
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="form-group col-md-6">
                            <label for="mode-paiement" class="form-label">Mode de Paiement:</label>
                            <select class="form-control" id="mode-paiement">
                                <option value="">Sélectionner un mode du paiement</option>
                                @foreach ($modes_paiement as $mode)
                                    <option value="{{ $mode->id }}">{{ $mode->nom }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="date-paiement" class="form-label">Date de Paiement:</label>
                            <input type="date" class="form-control" id="date-paiement">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-info" onclick="addEtudiantAndPaiement()">Ajouter Etudiant et Paiement</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>


<!-- Add Payment Modal -->
<div class="modal fade" id="paiementAddModal" tabindex="-1" aria-labelledby="paiementAddModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paiementAddModalLabel">Ajouter un Paiement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="etudiant-id">
                <input type="hidden" id="etudiant-session-id">
                <input type="hidden" id="prix-formation">
                <input type="hidden" id="prix-reel">
                <input type="hidden" id="reste-a-payer">
                <div class="form-group">
                    <label for="nouveau-montant-paye" class="form-label">Nouveau Montant Payé:</label>
                    <input type="number" class="form-control" id="nouveau-montant-paye" placeholder="Entrez le montant payé">
                </div>
                <div class="form-group">
                    <label for="nouveau-mode-paiement" class="form-label">Mode de Paiement:</label>
                    <select class="form-control" id="nouveau-mode-paiement">
                        <option value="">Sélectionner un mode du paiement</option>
                        @foreach ($modes_paiement as $mode)
                            <option value="{{ $mode->id }}">{{ $mode->nom }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="nouveau-date-paiement" class="form-label">Date de Paiement:</label>
                    <input type="date" class="form-control" id="nouveau-date-paiement" name="date_paiement">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-info" onclick="addPaiement()">Ajouter Paiement</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>


<!-- Add Professor Modal -->
<div class="modal fade" id="profAddModal" tabindex="-1" aria-labelledby="profAddModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="profAddModalLabel">Ajouter un professeur à la session</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="new-prof-session_id">
                <div class="form-group">
                    <label for="prof-phone-search" class="form-label">Numéro de téléphone du professeur:</label>
                    <input type="text" class="form-control" id="prof-phone-search" placeholder="Entrez le numéro de téléphone">
                </div>
                <button type="button" class="btn btn-primary" onclick="searchProfByPhone()">Rechercher</button>
                <div id="prof-search-results"></div>
                <div id="prof-payment-form" style="display:none;">
                    <div class="row mb-3">
                        <div class="form-group col-md-4">
                            <label for="prof-typeymntprofs" class="form-label">Type de Paiement:</label>
                            <select class="form-control" id="prof-typeymntprofs" onchange="updatePaymentFields()">
                                <option value="">Sélectionner un type de contrat</option>
                                @foreach ($typeymntprofs as $type)
                                    <option value="{{ $type->id }}">{{ $type->type }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="prof-montant" class="form-label">Montant:</label>
                            <input type="text" class="form-control" id="prof-montant" placeholder="Entrez le montant">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="prof-montant_a_paye" class="form-label">Montant à Payer:</label>
                            <input type="text" class="form-control" id="prof-montant_a_paye" placeholder="Entrez le montant à payer">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="form-group col-md-4">
                            <label for="prof-montant_paye" class="form-label">Montant Payé:</label>
                            <input type="text" class="form-control" id="prof-montant_paye" placeholder="Entrez le montant payé">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="prof-mode-paiement" class="form-label">Mode de Paiement:</label>
                            <select class="form-control" id="prof-mode-paiement">
                                <option value="">Sélectionner un mode du paiement</option>
                                @foreach ($modes_paiement as $mode)
                                    <option value="{{ $mode->id }}">{{ $mode->nom }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="prof-date-paiement" class="form-label">Date de Paiement:</label>
                            <input type="date" class="form-control" id="prof-date-paiement">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-info" onclick="addProfAndPaiement()">Ajouter Professeur et Paiement</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>


<!-- Modal Ajouter Paiement pour Professeur -->
<div class="modal fade" id="profPaiementAddModal" tabindex="-1" aria-labelledby="profPaiementAddModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="profPaiementAddModalLabel">Ajouter un Paiement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="prof-id">
                <input type="hidden" id="prof-session-id">
                <input type="hidden" id="prof-montant">
                <input type="hidden" id="prof-montant_a_paye">
                <input type="hidden" id="prof-reste-a-payer">
                <input type="hidden" id="prof-typeymntprofs">
                <div class="mb-3">
                    <label for="prof-nouveau-montant-paye" class="form-label">Montant Payé</label>
                    <input type="number" class="form-control" id="prof-nouveau-montant-paye" placeholder="Entrez le montant payé" required>
                </div>
                <div class="mb-3">
                    <label for="nouveau-prof-mode-paiement" class="form-label">Mode de Paiement</label>
                    <select class="form-select" id="nouveau-prof-mode-paiement" required>
                        <option value="">Sélectionner un mode du paiement</option>
                        @foreach ($modes_paiement as $mode)
                            <option value="{{ $mode->id }}">{{ $mode->nom }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label for="nouveau-prof-date-paiement" class="form-label">Date de Paiement</label>
                    <input type="date" class="form-control" id="nouveau-prof-date-paiement" name="date_paiement" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-info" onclick="addProfPaiement()">Ajouter</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>



<div id="formationContentContainer" style="display:none;">
    <center><h6><span id="session-info"></span></h6></center>
    <div id="formationContents"></div>
</div>

<div id="formationProfContentContainer" style="display:none;">
    <center><h6><span id="prof-session-info"></span></h6></center>
    <div id="formationProfContents"></div>
</div>


<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            @if (session('status'))
            <div class="alert alert-success fade-out">
                {{ session('status')}}
            </div>
            @endif
            @if ($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </div>
            @endif
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 d-flex justify-content-between align-items-center">
                    <div>
                        <button type="button" class="btn bg-gradient-dark" data-bs-toggle="modal" data-bs-target="#sessionAddModal">
                            <i class="material-icons text-sm">add</i>&nbsp;&nbsp;Ajouter 
                        </button>
                        <a href="{{ route('sessions.export') }}" class="btn btn-success">Exporter </a>
                    </div>
                    <form class="d-flex align-items-center ms-auto">
                        <div class="input-group input-group-sm" style="width: 250px;">
                            <input type="text" name="search6" id="search_bar" class="form-control" placeholder="Rechercher..." value="{{ isset($search6) ? $search6 : '' }}">
                        </div>
                    </form>
                                    </div>
                                    <div class="card-body px-0 pb-2">
                        <div class="table-responsive p-0" id="sessions-table">
                            @include('livewire.example-laravel.sessions-list', ['sessions' => $sessions])
                        </div>
                    </div>
            </div>
        </div>
    </div>
</div>


<!-- Add Session Modal -->
<div class="modal fade" id="sessionAddModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Ajouter une nouvelle Formation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="session-add-form">
                    @csrf
                    <div class="row mb-2">
                        <div class="form-group col-md-6">
                            <label for="formation_id" class="form-label required">Programme</label>
                            <select class="form-control" id="new-session-formation_id" name="formation_id">
                                <option value="">Sélectionner Programme</option>
                                @foreach ($formations as $formation)
                                    <option value="{{ $formation->id }}">{{ $formation->nom }}</option>
                                @endforeach
                            </select>
                            <div class="text-danger" id="formation_id-warning"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="nom" class="form-label">Nom:</label>
                            <input type="text" class="form-control" id="new-session-nom" placeholder="nom" name="nom">
                            <div class="text-danger" id="nom-warning"></div>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <label for="date_debut" class="form-label required">Date début:</label>
                            <input type="date" class="form-control" id="new-session-date_debut" name="date_debut">
                            <div class="text-danger" id="date_debut-warning"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="date_fin" class="form-label required">Date fin:</label>
                            <input type="date" class="form-control" id="new-session-date_fin" name="date_fin">
                            <div class="text-danger" id="date_fin-warning"></div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-info" id="add-new-session">Ajouter</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>


<!-- Edit Session Modal -->
<div class="modal fade" id="sessionEditModal" tabindex="-1" aria-labelledby="sessionEditModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sessionEditModalLabel">Modifier Formation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="session-edit-form">
                    @csrf
                    <input type="hidden" id="session-id" name="id">
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <label for="session-formation_id" class="form-label required">Programme :</label>
                            <select class="form-control" id="session-formation_id" name="formation_id">
                                <option value="">Sélectionner Programme</option>
                                @foreach ($formations as $formation)
                                    <option value="{{ $formation->id }}">{{ $formation->nom }}</option>
                                @endforeach
                            </select>
                            <div class="text-danger" id="edit-formation_id-warning"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="session-nom" class="form-label required">Nom :</label>
                            <input type="text" class="form-control" id="session-nom" name="nom">
                            <div class="text-danger" id="edit-nom-warning"></div>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <label for="session-date_debut" class="form-label required">Date début :</label>
                            <input type="date" class="form-control" id="session-date_debut" name="date_debut">
                            <div class="text-danger" id="edit-date_debut-warning"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="session-date_fin" class="form-label required">Date fin :</label>
                            <input type="date" class="form-control" id="session-date_fin" name="date_fin">
                            <div class="text-danger" id="edit-date_fin-warning"></div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-info" id="session-update">Modifier</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">
$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    function getSessionDates(sessionId) {
        return $.ajax({
            url: `/sessions/${sessionId}/dates`,
            type: 'GET'
        });
    }

    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });

    $('#search_bar').on('keyup', function(){
        var query = $(this).val();
        $.ajax({
            url: "{{ route('search6') }}",
            type: "GET",
            data: {'search6': query},
            success: function(data){
                $('#sessions-table').html(data.html);
            }
        });
    });

    
    $("#add-new-session").click(function(e){
        e.preventDefault();

        // Validation des champs requis
        let isValid = true;

        if ($('#new-session-formation_id').val().trim() === '') {
            isValid = false;
            $('#new-session-formation_id').addClass('is-invalid');
            $('#formation_id-warning').text('Ce champ est requis.');
        } else {
            $('#new-session-formation_id').removeClass('is-invalid');
            $('#formation_id-warning').text('');
        }

        if ($('#new-session-nom').val().trim() === '') {
            isValid = false;
            $('#new-session-nom').addClass('is-invalid');
            $('#nom-warning').text('Ce champ est requis.');
        } else {
            $('#new-session-nom').removeClass('is-invalid');
            $('#nom-warning').text('');
        }

        if ($('#new-session-date_debut').val().trim() === '') {
            isValid = false;
            $('#new-session-date_debut').addClass('is-invalid');
            $('#date_debut-warning').text('Ce champ est requis.');
        } else {
            $('#new-session-date_debut').removeClass('is-invalid');
            $('#date_debut-warning').text('');
        }

        if ($('#new-session-date_fin').val().trim() === '') {
            isValid = false;
            $('#new-session-date_fin').addClass('is-invalid');
            $('#date_fin-warning').text('Ce champ est requis.');
        } else {
            $('#new-session-date_fin').removeClass('is-invalid');
            $('#date_fin-warning').text('');
        }

        if (!isValid) {
            return;
        }

        let form = $('#session-add-form')[0];
        let data = new FormData(form);

        $.ajax({
            url: "{{ route('session.store') }}",
            type: "POST",
            data: data,
            dataType: "JSON",
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.error) {
                    if (response.error === 'Le nom de session existe déjà.') {
                        $('#new-session-nom').addClass('is-invalid');
                        $('#nom-warning').text(response.error);
                    } else {
                        iziToast.error({
                            title: 'Erreur',
                            message: response.error,
                            position: 'topRight'
                        });
                    }
                } else {
                    iziToast.success({
                        message: response.success,
                        position: 'topRight'
                    });
                    $('#sessionAddModal').modal('hide');
                    location.reload();
                }
            },
            error: function(xhr, status, error) {
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    if (xhr.status === 409) { // Conflit
                        $('#new-session-nom').addClass('is-invalid');
                        $('#nom-warning').text(xhr.responseJSON.error);
                    } else {
                        iziToast.error({
                            title: 'Erreur',
                            message: xhr.responseJSON.error,
                            position: 'topRight'
                        });
                    }
                } else {
                    let errorMsg = 'Une erreur est survenue : ' + error;
                    iziToast.error({
                        title: 'Erreur',
                        message: errorMsg,
                        position: 'topRight'
                    });
                }
            }
        });
    });




    // Modifier une session
    $('body').on('click', '.btn-info', function () {
        var sessionId = $(this).data('id'); // Utilisez le data attribute pour récupérer l'ID
        $.get('/sessions/' + sessionId, function (data) {
            $('#session-id').val(data.session.id);
            $('#session-formation_id').val(data.session.formation_id);
            $('#session-nom').val(data.session.nom);
            $('#session-date_debut').val(data.session.date_debut);
            $('#session-date_fin').val(data.session.date_fin);
            $('#sessionEditModal').modal('show');
        });
    });

    $('#session-update').click(function (e) {
        e.preventDefault();
        let id = $('#session-id').val();
        let form = $('#session-edit-form')[0];
        let data = new FormData(form);
        data.append('_method', 'PUT');

        $.ajax({
            url: '/sessions/' + id,
            type: 'POST',
            data: data,
            dataType: 'json',
            processData: false,
            contentType: false,
            success: function (response) {
                if (response.error) {
                    iziToast.error({
                        title: 'Erreur',
                        message: response.error,
                        position: 'topRight'
                    });
                } else {
                    iziToast.success({
                        title: 'Succès',
                        message: response.success,
                        position: 'topRight'
                    });
                    $('#sessionEditModal').modal('hide');
                    setTimeout(function () {
                        location.reload();
                    }, 1000);
                }
            }
        });
    });



    $('body').on('click', '#delete-session', function (e) {
        e.preventDefault();
        var id = $(this).data('id');

        $.ajax({
            url: "{{ route('session.delete', '') }}/" + id,
            type: 'DELETE',
            success: function(response) {
                if (response.status === 400) {
                    iziToast.error({
                        message: response.message,
                        position: 'topRight'
                    });
                } else {
                    iziToast.success({
                        message: response.success,
                        position: 'topRight'
                    });
                    location.reload();
                }
            },
            error: function(xhr, status, error) {
                iziToast.error({
                    message: 'An error occurred: ' + error,
                    position: 'topRight'
                });
            }
        });
    });

    window.setSessionId = function(sessionId) {
        $('#new-student-session_id').val(sessionId);
    }

    window.searchStudentByPhone = function() {
        const phone = $('#student-phone-search').val();
        const sessionId = $('#new-student-session_id').val();

        if (phone) {
            $.ajax({
                url: '{{ route("etudiant.search") }}',
                type: 'POST',
                data: { phone: phone },
                success: function(response) {
                    if (response.etudiant) {
                        const etudiant = response.etudiant;
                        $.ajax({
                            url: `/sessions/${sessionId}/check-student`,
                            type: 'POST',
                            data: { etudiant_id: etudiant.id },
                            success: function(checkResponse) {
                                if (checkResponse.isInSession) {
                                    $('#student-search-results').html('<div class="alert alert-danger">L\'étudiant est déjà inscrit dans cette Formation.</div>');
                                } else {
                                    $('#student-search-results').html(
                                        `<div class="alert alert-success">Etudiant trouvé: ${etudiant.nomprenom}</div>
                                        <input type="hidden" id="etudiant-id" value="${etudiant.id}">`
                                    );
                                    loadFormationDetails();
                                    $('#payment-form').show();
                                }
                            },
                            error: function(xhr, status, error) {
                                alert('Erreur lors de la vérification de l\'étudiant dans la Formation: ' + error);
                            }
                        });
                    } else {
                        $('#student-search-results').html('<div class="alert alert-danger">Etudiant non trouvé.</div>');
                    }
                },
                error: function(xhr, status, error) {
                    alert('Erreur lors de la recherche de l\'étudiant: ' + error);
                }
            });
        } else {
            alert('Veuillez entrer un numéro de téléphone.');
        }
    }

    window.loadFormationDetails = function() {
        const sessionId = $('#new-student-session_id').val();
        $.ajax({
            url: "{{ route('sessions.details', ':sessionId') }}".replace(':sessionId', sessionId),
            type: 'GET',
            success: function(response) {
                if (response.formation) {
                    $('#formation-price').val(response.formation.prix);
                    $('#prix-reel').val(response.formation.prix); // Set Prix Réel to Prix Programme
                    const today = new Date().toISOString().split('T')[0];
                    $('#date-paiement').val(today); // Set Date de Paiement to today's date
                } else {
                    $('#formation-price').val('');
                    $('#prix-reel').val(''); // Clear Prix Réel if no formation data is found
                }
            },
            error: function(xhr, status, error) {
                alert('Erreur lors du chargement des détails de la formation: ' + error);
            }
        });
    }

    window.openAddPaymentModal = function(etudiantId, sessionId) {
        $.ajax({
            url: `/sessions/${sessionId}/etudiants/${etudiantId}/details`,
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    const resteAPayer = response.reste_a_payer;
                    if (resteAPayer <= 0) {
                        iziToast.warning({
                            message: 'L\'étudiant a déjà payé la totalité de la formation.',
                            position: 'topRight'
                        });
                    } else {
                        $('#etudiant-id').val(etudiantId);
                        $('#etudiant-session-id').val(sessionId);
                        $('#prix-formation').val(response.prix_formation);
                        $('#prix-reel').val(response.prix_reel);
                        $('#reste-a-payer').val(resteAPayer);
                         // Set the date field to today's date
                        const today = new Date().toISOString().split('T')[0];
                        $('#nouveau-date-paiement').val(today);
                        $('#paiementAddModal').modal('show');
                    }
                } else {
                    iziToast.error({
                        message: response.error,
                        position: 'topRight'
                    });
                }
            },
            error: function(xhr, status, error) {
                iziToast.error({
                    message: 'Erreur lors de la récupération des détails: ' + error,
                    position: 'topRight'
                });
            }
        });
    };

    window.addEtudiantAndPaiement = function() {
        const etudiantId = $('#etudiant-id').val();
        const sessionId = $('#new-student-session_id').val();
        const datePaiement = $('#date-paiement').val();
        const montantPaye = $('#montant-paye').val();
        const modePaiement = $('#mode-paiement').val();
        const prixReel = $('#prix-reel').val();

        if (!etudiantId || !sessionId) {
            alert('Etudiant ID or Session ID is missing.');
            return;
        }

        $.ajax({
            url: `/sessions/${sessionId}/etudiants/${etudiantId}/add`,
            type: 'POST',
            data: {
                etudiant_id: etudiantId,
                date_paiement: datePaiement,
                montant_paye: montantPaye,
                mode_paiement: modePaiement,
                prix_reel: prixReel
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    $('#etudiantAddModal').modal('hide');
                    showContents(sessionId);
                } else {
                    alert(response.error);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                console.error('Status:', status);
                console.error('Response:', xhr.responseText);
                alert('Erreur lors de l\'ajout de l\'étudiant: ' + xhr.responseText);
            }
        });
    }


    window.addPaiement = function() {
        const etudiantId = $('#etudiant-id').val();
        const sessionId = $('#etudiant-session-id').val();
        const nouveauMontantPaye = $('#nouveau-montant-paye').val();
        const modePaiement = $('#nouveau-mode-paiement').val();
        const datePaiement = $('#nouveau-date-paiement').val();

        // Log the values to check if datePaiement is correctly retrieved
        console.log({
            etudiantId,
            sessionId,
            nouveauMontantPaye,
            modePaiement,
            datePaiement
        });

        $.ajax({
            url: `/sessions/${sessionId}/paiements`,
            type: 'POST',
            data: {
                etudiant_id: etudiantId,
                montant_paye: nouveauMontantPaye,
                mode_paiement: modePaiement,
                date_paiement: datePaiement  // Ensure this field is included
            },
            success: function(response) {
                if (response.success) {
                    $('#paiementAddModal').modal('hide');
                    showContents(sessionId);
                } else {
                    alert(response.error);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                console.error('Status:', status);
                console.error('Response:', xhr.responseText);
                alert('Erreur lors de l\'ajout du paiement de l\'etudiant: ' + xhr.responseText);
            }
        });
    };


    window.deleteStudentFromSession = function(etudiantId, sessionId) {
        if (confirm("Êtes-vous sûr de vouloir supprimer cet étudiant de la Formation ?")) {
            $.ajax({
                url: `/sessions/${sessionId}/etudiants/${etudiantId}`,
                type: 'DELETE',
                success: function(response) {
                    if (response.success) {
                        iziToast.success({
                            message: response.success,
                            position: 'topRight'
                        });
                        showContents(sessionId);
                    } else {
                        iziToast.error({
                            message: response.error,
                            position: 'topRight'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    iziToast.error({
                        message: 'Erreur lors de la suppression: ' + error,
                        position: 'topRight'
                    });
                }
            });
        }
    };

    window.showContents = function(sessionId) {
        $.ajax({
            url: `/sessions/${sessionId}/contents`,
            type: 'GET',
            success: function(response) {
                if (response.error) {
                    alert(response.error);
                    return;
                }

                let html = `<div class="container-fluid ">
                    <div class="row">
                        <div class="col-12">
                            <div class="card my-4">
                                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 d-flex justify-content-between align-items-center">
                                    <div>
                                        <button class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#etudiantAddModal" onclick="setSessionId(${sessionId})" data-toggle="tooltip" title="Ajouter un étudiant"><i class="material-icons opacity-10">add</i></button>
                                        <button class="btn btn-secondary" onclick="hideStudentContents()">Fermer</button>
                                    </div>
                                </div>
                                <div class="card-body px-0 pb-2">
                                    <div class="table-responsive p-0" id="sessions-table">
                                        <table class="table align-items-center mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Nom & Prénom</th>
                                                    <th>Phone</th>
                                                    <th>WhatsApp</th>
                                                    <th>Prix Programme</th>
                                                    <th>Prix Réel</th>
                                                    <th>Montant Payé</th>
                                                    <th>Reste à Payer</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>`;

                if (response.etudiants.length > 0) {
                    response.etudiants.forEach(function(content) {
                        let resteAPayer = content.prix_reel - content.montant_paye;

                        html += `<tr>
                            <td>${content.nomprenom}</td>
                            <td>${content.phone}</td>
                            <td>${content.wtsp}</td>
                            <td>${content.prix_formation}</td>
                            <td>${content.prix_reel}</td>
                            <td>${content.montant_paye}</td>
                            <td>${resteAPayer}</td>
                            <td>
                                <button class="btn btn-dark" onclick="openAddPaymentModal(${content.id}, ${sessionId})"><i class="material-icons opacity-10">payment</i></button>
                                <button class="btn btn-danger" onclick="deleteStudentFromSession(${content.id}, ${sessionId})"><i class="material-icons opacity-10">delete_forever</i></button>
                                <a href="/sessions/${sessionId}/generateReceipt/${content.id}" class="btn btn-info">
                                    <i class="material-icons opacity-10">download</i>
                                </a>

                            </td>
                        </tr>`;
                    });
                } else {
                    html += '<tr><td colspan="8" class="text-center">Aucun étudiant trouvé pour cette session.</td></tr>';
                }

                html += `</tbody></table></div></div></div></div></div>`;
                $('#formationContents').html(html);
                $('#formationContentContainer').show();

                // Display session info and statistics
                // $('#session-info').html(`<h5>Liste des étudiants de la Formation: "${response.session_nom}" du Programme : ${response.formation_nom} </h5>  Nombre de Étudiants: ${response.total_etudiants} | Total Prix Réel: ${response.total_prix_reel} | Total Montant Payé: ${response.total_montant_paye} | Reste à Payer: ${response.total_reste_a_payer}  `);
                $('#session-info').html(`
                    <div class="container-fluid">
                        <div style="border: 1px solid #ccc; padding: 10px; border-radius: 8px; background-color: #fff; margin-bottom: 10px;">
                            <h5 style="font-size: 16px; font-weight: bold; color: #333; margin-bottom: 10px;">
                                Liste des étudiants de la Formation: <span style="color: #007bff;">"${response.session_nom}"</span> du Programme : <span style="color: #007bff;">${response.formation_nom}</span>
                            </h5>
                            <div style="display: flex; flex-wrap: wrap; gap: 5px;">
                                <div style="flex: 1; min-width: 150px; padding: 10px; border: 1px solid #ccc; border-radius: 8px; background-color: #fff;">
                                    <p style="font-size: 12px; color: #555; margin: 0;">
                                        <strong>Nombre de Étudiants:</strong>
                                    </p>
                                    <p style="font-size: 14px; color: #007bff; margin: 5px 0;">${response.total_etudiants}</p>
                                </div>
                                <div style="flex: 1; min-width: 150px; padding: 10px; border: 1px solid #ccc; border-radius: 8px; background-color: #fff;">
                                    <p style="font-size: 12px; color: #555; margin: 0;">
                                        <strong>Total Prix Réel:</strong>
                                    </p>
                                    <p style="font-size: 14px; color: #007bff; margin: 5px 0;">${response.total_prix_reel}</p>
                                </div>
                                <div style="flex: 1; min-width: 150px; padding: 10px; border: 1px solid #ccc; border-radius: 8px; background-color: #fff;">
                                    <p style="font-size: 12px; color: #555; margin: 0;">
                                        <strong>Total Montant Payé:</strong>
                                    </p>
                                    <p style="font-size: 14px; color: #007bff; margin: 5px 0;">${response.total_montant_paye}</p>
                                </div>
                                <div style="flex: 1; min-width: 150px; padding: 10px; border: 1px solid #ccc; border-radius: 8px; background-color: #fff;">
                                    <p style="font-size: 12px; color: #555; margin: 0;">
                                        <strong>Reste à Payer:</strong>
                                    </p>
                                    <p style="font-size: 14px; color: #007bff; margin: 5px 0;">${response.total_reste_a_payer}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                `);





                $('html, body').animate({ scrollTop: $('#formationContentContainer').offset().top }, 'slow');
            },
            error: function(xhr, status, error) {
                alert('Erreur lors du chargement des contenus: ' + error);
            }
        });
    }

    window.hideStudentContents = function() {
        $('#formationContentContainer').hide();
        $('html, body').animate({ scrollTop: 0 }, 'slow');
    };

    window.setProfSessionId = function(sessionId) {
        $('#new-prof-session_id').val(sessionId);
    }

    window.searchProfByPhone = function() {
        const phone = $('#prof-phone-search').val();
        const sessionId = $('#new-prof-session_id').val();

        if (phone) {
            $.ajax({
                url: '{{ route("professeur.search") }}',
                type: 'POST',
                data: { phone: phone },
                success: function(response) {
                    if (response.professeur) {
                        const professeur = response.professeur;
                        $.ajax({
                            url: `/sessions/${sessionId}/check-prof`,
                            type: 'POST',
                            data: { prof_id: professeur.id },
                            success: function(checkResponse) {
                                if (checkResponse.isInSession) {
                                    $('#prof-search-results').html('<div class="alert alert-danger">Le professeur est déjà inscrit dans cette session.</div>');
                                } else {
                                    $('#prof-search-results').html(
                                        `<div class="alert alert-success">Professeur trouvé: ${professeur.nomprenom}</div>
                                        <input type="hidden" id="prof-id" value="${professeur.id}">`
                                    );
                                    loadProfSessionDetails(sessionId);
                                    $('#prof-payment-form').show();
                                }
                            },
                            error: function(xhr, status, error) {
                                alert('Erreur lors de la vérification du professeur dans la session: ' + error);
                            }
                        });
                    } else {
                        $('#prof-search-results').html('<div class="alert alert-danger">Professeur non trouvé.</div>');
                    }
                },
                error: function(xhr, status, error) {
                    alert('Erreur lors de la recherche du professeur: ' + error);
                }
            });
        } else {
            alert('Veuillez entrer un numéro de téléphone.');
        }
    }

    window.loadProfSessionDetails = function(sessionId) {
        const today = new Date().toISOString().split('T')[0];
        $('#prof-date-paiement').val(today); // Set Date de Paiement to today's date
    };


    window.openAddProfPaymentModal = function(profId, sessionId) {
        $.ajax({
            url: `/sessions/${sessionId}/profs/${profId}/details`,
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    const resteAPayer = response.reste_a_payer;
                    if (resteAPayer <= 0) {
                        iziToast.warning({
                            message: 'Le professeur a déjà payé la totalité de la formation.',
                            position: 'topRight'
                        });
                    } else {
                        $('#prof-id').val(profId);
                        $('#prof-session-id').val(sessionId);
                        $('#prof-montant').val(response.montant);
                        $('#prof-montant_a_paye').val(response.montant_a_paye);
                        $('#prof-reste-a-payer').val(resteAPayer);
                        $('#prof-typeymntprofs').val(response.type_paiement); // Assurez-vous que cette valeur est correcte
                         // Set the date field to today's date
                        const today = new Date().toISOString().split('T')[0];
                        $('#nouveau-prof-date-paiement').val(today);
                        $('#profPaiementAddModal').modal('show');
                    }
                } else {
                    iziToast.error({
                        message: response.error,
                        position: 'topRight'
                    });
                }
            },
            error: function(xhr, status, error) {
                iziToast.error({
                    message: 'Erreur lors de la récupération des détails: ' + error,
                    position: 'topRight'
                });
            }
        });
    }

    window.addProfPaiement = function() {
        let profId = $('#prof-id').val();
        let sessionId = $('#prof-session-id').val();
        let nouveauMontantPaye = $('#prof-nouveau-montant-paye').val();
        let modePaiement = $('#nouveau-prof-mode-paiement').val();
        let datePaiement = $('#nouveau-prof-date-paiement').val();
        let montant = $('#prof-montant').val();
        let montantAPaye = $('#prof-montant_a_paye').val();
        let typeymntprofsId = $('#prof-typeymntprofs').val();

        if (!datePaiement) {
            alert('Veuillez sélectionner une date de paiement.');
            return;
        }

        $.ajax({
            url: `/sessions/${sessionId}/profpaiements`,
            type: 'POST',
            data: {
                prof_id: profId,
                montant_paye: nouveauMontantPaye,
                mode_paiement: modePaiement,
                date_paiement: datePaiement,
                typeymntprofs_id: typeymntprofsId,
                montant: montant,
                montant_a_paye: montantAPaye
            },
            success: function(response) {
                if (response.success) {
                    $('#profPaiementAddModal').modal('hide');
                    showProfContents(sessionId);
                } else {
                    alert(response.error);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                console.error('Status:', status);
                console.error('Response:', xhr.responseText);
                alert('Erreur lors de l\'ajout du paiement du professeur: ' + xhr.responseText);
            }
        });
    }


    window.updatePaymentFields = function() {
        const typeId = $('#prof-typeymntprofs').val();
        const montantField = $('#prof-montant');
        const montantAPayeField = $('#prof-montant_a_paye');
        const sessionId = $('#new-prof-session_id').val();
        montantField.val(''); // Clear the montant field

        if (typeId == '1') { // Assuming typeymntprofs_id 1 is for percentage
            montantField.attr('placeholder', 'Entrez le pourcentage');
            montantField.attr('max', '100');
            montantField.attr('min', '0');
            montantField.on('input', function() {
                const value = parseInt(this.value, 10);
                if (value < 0 || value > 100) {
                    alert('Le pourcentage doit être compris entre 0 et 100');
                    this.value = '';
                } else {
                    $.ajax({
                        url: `/sessions/${sessionId}/total-student-payments`,
                        type: 'GET',
                        success: function(response) {
                            const totalStudentPayments = response.total;
                            const calculatedMontantAPaye = Math.round((totalStudentPayments * value) / 100);
                            montantAPayeField.val(calculatedMontantAPaye);
                        },
                        error: function(xhr, status, error) {
                            alert('Erreur lors de la récupération des paiements des étudiants: ' + error);
                        }
                    });
                }
            });
        } else if (typeId == '2') { // Assuming typeymntprofs_id 2 is for monthly
            montantField.attr('placeholder', 'Entrez le salaire mensuel');
            montantField.removeAttr('max min');
            montantField.off('input').on('input', function() {
                const monthlySalary = parseInt(this.value, 10);
                if (isNaN(monthlySalary) || monthlySalary <= 0) {
                    montantAPayeField.val('');
                    return;
                }

                getSessionDates(sessionId).done(function(response) {
                    const startDate = new Date(response.start_date);
                    const endDate = new Date(response.end_date);
                    const diffTime = Math.abs(endDate - startDate);
                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                    const months = diffDays / 30;
                    const calculatedMontantAPaye = Math.round(monthlySalary * months);
                    montantAPayeField.val(calculatedMontantAPaye);
                }).fail(function(jqXHR, textStatus) {
                    alert('Erreur lors de la récupération des dates de session: ' + textStatus);
                });
            });
        } else if (typeId == '3') { // Assuming typeymntprofs_id 3 is for hourly
            montantField.attr('placeholder', 'Entrez le tarif horaire');
            montantField.removeAttr('max min');
            montantField.off('input').on('input', function() {
                const hourlyRate = parseInt(this.value, 10);
                if (isNaN(hourlyRate) || hourlyRate <= 0) {
                    montantAPayeField.val('');
                    return;
                }

                const totalHours = 1; // This should be fetched dynamically
                const calculatedMontantAPaye = Math.round(hourlyRate * totalHours);
                montantAPayeField.val(calculatedMontantAPaye);
            });
        }
    }



    window.addProfAndPaiement = function() {
        const profId = $('#prof-id').val();
        const sessionId = $('#new-prof-session_id').val();
        const datePaiement = $('#prof-date-paiement').val();
        const montantAPaye = $('#prof-montant_a_paye').val();
        const montantPaye = $('#prof-montant_paye').val();
        const modePaiement = $('#prof-mode-paiement').val();
        const montant = $('#prof-montant').val();
        const typeId = $('#prof-typeymntprofs').val();

        if (!profId || !sessionId) {
            alert('Professeur ID ou Session ID est manquant.');
            return;
        }

        $.ajax({
            url: `/sessions/${sessionId}/profs/${profId}/add`,
            type: 'POST',
            data: {
                prof_id: profId,
                date_paiement: datePaiement,
                montant_a_paye: montantAPaye,
                montant_paye: montantPaye,
                mode_paiement: modePaiement,
                montant: montant,
                typeymntprofs_id: typeId,
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    $('#profAddModal').modal('hide');
                    showProfContents(sessionId); // Refresh the list after adding
                } else {
                    alert(response.error);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                console.error('Status:', status);
                console.error('Response:', xhr.responseText);
                alert('Erreur lors de l\'ajout du professeur: ' + xhr.responseText);
            }
        });
    }

    window.deleteProfFromSession = function(profId, sessionId) {
        if (confirm("Êtes-vous sûr de vouloir supprimer ce professeur de la session ?")) {
            $.ajax({
                url: `/sessions/${sessionId}/profs/${profId}`,
                type: 'DELETE',
                success: function(response) {
                    if (response.success) {
                        iziToast.success({
                            message: response.success,
                            position: 'topRight'
                        });
                        showProfContents(sessionId); // Refresh the list after deleting
                    } else {
                        iziToast.error({
                            message: response.error,
                            position: 'topRight'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    iziToast.error({
                        message: 'Erreur lors de la suppression: ' + error,
                        position: 'topRight'
                    });
                }
            });
        }
    }


    window.showProfContents = function(sessionId) {
        $.ajax({
            url: `/sessions/${sessionId}/profcontents`,
            type: 'GET',
            success: function(response) {
                if (response.error) {
                    iziToast.error({ message: response.error, position: 'topRight' });
                    return;
                }

                let html = `<div class="container-fluid ">
                    <div class="row">
                        <div class="col-12">
                            <div class="card my-4">
                                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 d-flex justify-content-between align-items-center">
                                    <div>
                                        <button class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#profAddModal" onclick="setProfSessionId(${sessionId})" data-toggle="tooltip" title="Ajouter un professeur"><i class="material-icons opacity-10">add</i></button>
                                        <button class="btn btn-secondary" onclick="hideProfContents()">Fermer</button>
                                    </div>
                                </div>
                                <div class="card-body px-0 pb-2">
                                    <div class="table-responsive p-0" id="sessions-table">
                                        <table class="table align-items-center mb=0">
                                            <thead>
                                                <tr>
                                                    <th>Nom & Prénom</th>
                                                    <th>Phone</th>
                                                    <th>WhatsApp</th>
                                                    <th>Montant</th>
                                                    <th>Montant à Payer</th>
                                                    <th>Montant Payé</th>
                                                    <th>Reste à Payer</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>`;

                if (response.professeurs && response.professeurs.length > 0) {
                    response.professeurs.forEach(function(content) {
                        let montant = content.montant || 0;
                        let montant_a_paye = content.montant_a_paye || 0;
                        let montant_paye = content.montant_paye || 0;
                        let resteAPayer = montant_a_paye - montant_paye;

                        html += `<tr>
                            <td>${content.nomprenom || 'N/A'}</td>
                            <td>${content.phone || 'N/A'}</td>
                            <td>${content.wtsp || 'N/A'}</td>
                            <td>${montant}</td>
                            <td>${montant_a_paye}</td>
                            <td>${montant_paye}</td>
                            <td>${resteAPayer}</td>
                            <td>
                                <button class="btn btn-dark" onclick="openAddProfPaymentModal(${content.id}, ${sessionId})"><i class="material-icons opacity-10">payment</i></button>
                                <button class="btn btn-danger" onclick="deleteProfFromSession(${content.id}, ${sessionId})"><i class="material-icons opacity-10">delete_forever</i></button>
                                 <a href="/sessions/${sessionId}/generateProfReceipt/${content.id}" class="btn btn-info">
                                    <i class="material-icons opacity-10">download</i>
                                </a>
                            </td>
                        </tr>`;
                    });
                } else {
                    html += '<tr><td colspan="8" class="text-center">Aucun professeur trouvé pour cette session.</td></tr>';
                }

                html += `</tbody></table></div></div></div></div></div>`;
                $('#formationProfContents').html(html);
                $('#formationProfContentContainer').show();
                // $('#prof-session-info').html(`<h5>Liste des Professeurs de la Formation: ${response.prof_session_nom} du Programme: ${response.prof_formation_nom}</h5> Nombre de Professeurs: ${response.total_profs} | Total Montant à Payer: ${response.prof_total_montant_a_paye} | Total Montant Payé: ${response.prof_total_montant_paye} | Reste à Payer: ${response.prof_total_reste_a_payer}  `);
                $('#prof-session-info').html(`
                    <div class="container-fluid ">
                        <div style="border: 1px solid #ccc; padding: 10px; border-radius: 8px; background-color: #fff; margin-bottom: 10px;">
                            <h5 style="font-size: 16px; font-weight: bold; color: #333; margin-bottom: 10px;">
                                Liste des Professeurs  Formation: <span style="color: #007bff;">"${response.prof_session_nom}"</span>  Programme : <span style="color: #007bff;">${response.prof_formation_nom}</span>
                            </h5>
                            <div style="display: flex; flex-wrap: wrap; gap: 5px;">
                                <div style="flex: 1; min-width: 150px; padding: 10px; border: 1px solid #ccc; border-radius: 8px; background-color: #fff;">
                                    <p style="font-size: 12px; color: #555; margin: 0;">
                                        <strong>Nombre de Professeurs:</strong>
                                    </p>
                                    <p style="font-size: 14px; color: #007bff; margin: 5px 0;">${response.total_profs}</p>
                                </div>
                                <div style="flex: 1; min-width: 150px; padding: 10px; border: 1px solid #ccc; border-radius: 8px; background-color: #fff;">
                                    <p style="font-size: 12px; color: #555; margin: 0;">
                                        <strong>Total Montant à Payer:</strong>
                                    </p>
                                    <p style="font-size: 14px; color: #007bff; margin: 5px 0;">${response.prof_total_montant_a_paye}</p>
                                </div>
                                <div style="flex: 1; min-width: 150px; padding: 10px; border: 1px solid #ccc; border-radius: 8px; background-color: #fff;">
                                    <p style="font-size: 12px; color: #555; margin: 0;">
                                        <strong>Total Montant Payé:</strong>
                                    </p>
                                    <p style="font-size: 14px; color: #007bff; margin: 5px 0;">${response.prof_total_montant_paye}</p>
                                </div>
                                <div style="flex: 1; min-width: 150px; padding: 10px; border: 1px solid #ccc; border-radius: 8px; background-color: #fff;">
                                    <p style="font-size: 12px; color: #555; margin: 0;">
                                        <strong>Reste à Payer:</strong>
                                    </p>
                                    <p style="font-size: 14px; color: #007bff; margin: 5px 0;">${response.prof_total_reste_a_payer}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                `);

                $('html, body').animate({ scrollTop: $('#formationProfContentContainer').offset().top }, 'slow');
            },
            error: function(xhr, status, error) {
                iziToast.error({ message: 'Erreur lors du chargement des contenus: ' + error, position: 'topRight' });
            }
        });
    }

    window.hideProfContents = function() {
        $('#formationProfContentContainer').hide();
        $('html, body').animate({ scrollTop: 0 }, 'slow');
    }

});
</script>
</body>
</html>
