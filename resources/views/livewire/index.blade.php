<div class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">
                        <a href="{{route('dashboard')}}">
                            <span><i class="fas fa-arrow-left mr-3"></i>Aktivitas</span>
                        </a>
                    </h4>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <button class="btn btn-primary" wire:click="tambahKriteria">Tambah Aktivitas</button>
                    <hr>
                    <table class="table table-bordered table-head-bg-info table-bordered-bd-info">
                        <thead>
                            <tr>
                                <th scope="col">Aktivitas</th>
                                <th scope="col">Kode</th>
                                <th scope="col">Kegiatan Pendahulu</th>
                                <th scope="col">Durasi</th>
                                <th scope="col">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($datas as $key => $data)
                            <tr>
                                <td>{{ $data->nama }}</td>
                                <td>{{ $data->kode }}</td>
                                <td>{{ $data->pendahulu }}</td>
                                <td>{{ $data->durasi }}</td>
                                <td><button class="btn btn-primary" wire:click="showModalEdit('{{ $data->id }}')">Edit</button>
                                    <button class="btn btn-danger" wire:click="delete('{{ $data->id }}')">Hapus</button></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <button class="btn btn-primary pull-right" wire:click = "prosesdata">Proses Data</button>
                    <button class="btn btn-danger pull-right" wire:click = "resetProses" style="margin-right: 1%">Reset Data</button>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h3>Nilai Keluaran</h3>
                    <hr>
                    <table class="table table-bordered table-head-bg-info table-bordered-bd-info">
                        <thead>
                            <tr>
                                <th scope="col">Aktivitas</th>
                                <th scope="col">Kode</th>
                                <th scope="col">Pendahulu</th>
                                <th scope="col">Penerus</th>
                                <th scope="col">Durasi</th>
                                <th scope="col">Early Start</th>
                                <th scope="col">Late Start</th>
                                <th scope="col">Early Finish</th>
                                <th scope="col">Late Finish</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($proses_data as $key => $proses)
                                @if ($proses->early_finish == $proses->late_finish)
                                <tr class="table-success">
                                @else
                                <tr>
                                @endif
                                    <td>{{ $proses->cpm->nama }}</td>
                                    <td>{{ $proses->cpm->kode }}</td>
                                    @if ($proses->cpm->pendahulu == null)
                                        <td>Mulai</td>
                                    @else
                                    <td>{{ $proses->cpm->pendahulu }}</td>
                                    @endif
                                    <td>{{ $proses->penerus }}</td>
                                    <td>{{ $proses->cpm->durasi }}</td>
                                    <td>{{ $proses->early_start }}</td>
                                    <td>{{ $proses->late_start }}</td>
                                    <td>{{ $proses->early_finish }}</td>
                                    <td>{{ $proses->late_finish }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <hr>
                    <h3>Critical Path</h3>
                    <hr>
                    <table class="table table-bordered table-head-bg-info table-bordered-bd-info">
                        <thead>
                            <tr>
                                <th scope="col" style="width: 3%">No</th>
                                <th scope="col">Aktivitas</th>
                                <th scope="col">Kode</th>
                                <th scope="col">Durasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $ct = 0;
                            @endphp
                            @foreach ($hasil_data as $key => $hasil)
                                <tr>
                                    <td>{{ $key+1 }}</td>
                                    <td>{{ $hasil->cpm->nama }}</td>
                                    <td>{{ $hasil->cpm->kode }}</td>
                                    <td>{{ $hasil->cpm->durasi }}</td>
                                </tr>
                                @php
                                    $ct = $ct + $hasil->cpm->durasi;
                                @endphp
                            @endforeach
                            <tr>
                                <td colspan="3">Critical Time</td>
                                <td>{{ $ct }}</td>
                            </tr>
                        </tbody>
                    </table>
                    <hr>
                </div>
            </div>
        </div>
    </div>

    <div id="modal-tambah" wire:ignore.self class="modal fade" tabindex="-1" role="dialog"
        aria-labelledby="my-modal-title" aria-hidden="true" class="justify-content-center">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="my-modal-title">Tambah Kriteria</h4>
                </div>
                <div class="modal-body">
                    <div
                        class="form-group {{$errors->has('namaKegiatan') ? 'has-error has-feedback' : '' }}">
                        <label for="namaKegiatan" class="placeholder"><b>Kegiatan</b></label>

                        <input id="namaKegiatan" name="namaKegiatan" wire:model="namaKegiatan" type="text"
                            class="form-control">
                        <small id="helpId{{'namaKegiatan'}}"
                            class="text-danger">{{ $errors->has('namaKegiatan') ? $errors->first('namaKegiatan') : '' }}</small>
                    </div>
                    <div
                        class="form-group {{$errors->has('kode') ? 'has-error has-feedback' : '' }}">
                        <label for="kode" class="placeholder"><b>Kode</b></label>

                        <input id="kode" name="kode" wire:model="kode" type="text"
                            class="form-control">
                        <small id="helpId{{'kode'}}"
                            class="text-danger">{{ $errors->has('kode') ? $errors->first('kode') : '' }}</small>
                    </div>
                    <div
                        class="form-group {{$errors->has('kegiatanPendahulu') ? 'has-error has-feedback' : '' }}">
                        <label for="kegiatanPendahulu" class="placeholder"><b>Kegiatan Pendahulu (Kode)</b></label>

                        <input id="kegiatanPendahulu" name="kegiatanPendahulu" wire:model="kegiatanPendahulu" type="text"
                            class="form-control">
                        <small id="helpId{{'kegiatanPendahulu'}}">Dipisahkan dengan koma tanpa spasi</small>
                        <small id="helpId{{'kegiatanPendahulu'}}"
                            class="text-danger">{{ $errors->has('kegiatanPendahulu') ? $errors->first('kegiatanPendahulu') : '' }}</small>
                    </div>
                    <div
                        class="form-group {{$errors->has('durasi') ? 'has-error has-feedback' : '' }}">
                        <label for="durasi" class="placeholder"><b>Durasi</b></label>

                        <input id="durasi" name="durasi" wire:model="durasi" type="text"
                            class="form-control">
                        <small id="helpId{{'durasi'}}"
                            class="text-danger">{{ $errors->has('durasi') ? $errors->first('durasi') : '' }}</small>
                    </div>
                </div>
                <div class="modal-footer justify-content-center">
                    <button class="btn btn-primary" wire:click="tambah">Tambah</button>
                </div>
            </div>
        </div>
    </div>

    @if ($isEdit)
    <div id="modal-edit" wire:ignore.self class="modal fade" tabindex="-1" role="dialog"
        aria-labelledby="my-modal-title" aria-hidden="true" class="justify-content-center">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="my-modal-title">Edit Kriteria</h4>
                </div>
                <div class="modal-body">
                    <div
                        class="form-group {{$errors->has('namaKegiatan') ? 'has-error has-feedback' : '' }}">
                        <label for="namaKegiatan" class="placeholder"><b>Kegiatan</b></label>

                        <input id="namaKegiatan" name="namaKegiatan" wire:model="namaKegiatan" type="text"
                            class="form-control">
                        <small id="helpId{{'namaKegiatan'}}"
                            class="text-danger">{{ $errors->has('namaKegiatan') ? $errors->first('namaKegiatan') : '' }}</small>
                    </div>
                    <div
                        class="form-group {{$errors->has('kode') ? 'has-error has-feedback' : '' }}">
                        <label for="kode" class="placeholder"><b>Kode</b></label>

                        <input id="kode" name="kode" wire:model="kode" type="text"
                            class="form-control">
                        <small id="helpId{{'kode'}}"
                            class="text-danger">{{ $errors->has('kode') ? $errors->first('kode') : '' }}</small>
                    </div>
                    <div
                        class="form-group {{$errors->has('kegiatanPendahulu') ? 'has-error has-feedback' : '' }}">
                        <label for="kegiatanPendahulu" class="placeholder"><b>Kegiatan Pendahulu (Kode)</b></label>

                        <input id="kegiatanPendahulu" name="kegiatanPendahulu" wire:model="kegiatanPendahulu" type="text"
                            class="form-control">
                        <small id="helpId{{'kegiatanPendahulu'}}">Dipisahkan dengan koma tanpa spasi</small>
                        <small id="helpId{{'kegiatanPendahulu'}}"
                            class="text-danger">{{ $errors->has('kegiatanPendahulu') ? $errors->first('kegiatanPendahulu') : '' }}</small>
                    </div>
                    <div
                        class="form-group {{$errors->has('durasi') ? 'has-error has-feedback' : '' }}">
                        <label for="durasi" class="placeholder"><b>Durasi</b></label>

                        <input id="durasi" name="durasi" wire:model="durasi" type="text"
                            class="form-control">
                        <small id="helpId{{'durasi'}}"
                            class="text-danger">{{ $errors->has('durasi') ? $errors->first('durasi') : '' }}</small>
                    </div>
                </div>
                <div class="modal-footer justify-content-center">
                    <button class="btn btn-primary" wire:click="edit">Simpan</button>
                </div>
            </div>
        </div>
    </div>
    @endif


    @push('scripts')
    <script src="{{ asset('assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js') }}"></script>
    @endpush
    <script>
        document.addEventListener('livewire:load', function (e) {
            e.preventDefault()

            window.livewire.on('showModalTambah', (data) => {
                // console.log(data)
                $('#modal-tambah').modal('show')
            });
            window.livewire.on('showModalEdit', (data) => {
                // console.log(data)
                $('#modal-edit').modal('show')
            });
            window.livewire.on('hideModal', (data) => {
                $('#modal-tambah').modal('hide')
                $('#modal-edit').modal('hide')
            });

        })

    </script>
</div>