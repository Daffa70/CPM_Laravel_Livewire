<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\DataCpm;
use App\Models\ProsesData;

class Index extends Component
{
    public $isEdit = false;
    public $namaKegiatan;
    public $kode;
    public $kegiatanPendahulu;
    public $durasi;
    public $id_cpm;

    public function render()
    {
        $datas = DataCpm::orderBy('created_at')->get();
        $proses_data = ProsesData::orderBy('created_at')->get();
        $hasil_data = ProsesData::whereColumn('early_finish', 'late_finish')->get();

        return view('livewire.index',[
            'datas' => $datas,
            'proses_data' => $proses_data,
            'hasil_data' => $hasil_data
        ])->layout('layouts.home');


    }

    public function tambahKriteria(){
        $this->emit('showModalTambah');
    }

    public function tambah(){

        $this->validate([
            'namaKegiatan' => 'required',
            'kode' => 'required',
            'durasi' => 'required|numeric'
        ]);

        DataCpm::create([
            'nama' => $this->namaKegiatan,
            'kode' => $this->kode,
            'pendahulu' => $this->kegiatanPendahulu,
            'durasi' => $this->durasi
        ]);


        $this->namaKegiatan = null;
        $this->kode = null;
        $this->kegiatanPendahulu = null;
        $this->durasi = null;

        $this->hideModal();
        $this->emit('showAlert', ['msg' => 'Aktivitas berhasil ditambah']);
        
    }

    public function delete($id){
        DataCpm::find($id)->delete();
        $this->emit('showAlert', ['msg' => 'Kriteria berhasil dihapus']);
    }

    public function showModalEdit($id){
        $this->id_cpm = $id;
        $this->isEdit = true;

        $kriteria = DataCpm::where('id', $id)->first();
        $this->namaKegiatan = $kriteria->nama;
        $this->kode = $kriteria->kode;
        $this->kegiatanPendahulu = $kriteria->pendahulu;
        $this->durasi = $kriteria->durasi;
        $this->emit('showModalEdit');
    }

    public function edit(){
        $this->validate([
            'namaKegiatan' => 'required',
            'kode' => 'required',
            'durasi' => 'required|numeric'
        ]);

        
        DataCpm::where('id', $this->id_cpm)->update([
            'nama' => $this->namaKegiatan,
            'kode' => $this->kode,
            'pendahulu' => $this->kegiatanPendahulu,
            'durasi' => $this->durasi
        ]);

        $this->namaKriteria = null;

        $this->emit('showAlert', ['msg' => 'Kriteria berhasil diedit']);
        $this->emit('hideModal');
    }


    public function hideModal(){
        $this->emit('hideModal');
    }

    public function prosesdata(){
        
        $data_cpm = DataCpm::orderBy('created_at')->get();

        foreach($data_cpm as $key => $data){
            ProsesData::create([
                'id_cpm' => $data->id,
                'penerus' => $this->getPenerus($data->id),
                'kode' => $data->kode,
                'early_start' => $this->earlyStart($data->id)
            ]);
        }

        foreach($data_cpm as $key => $data){
            $proses_data = ProsesData::where('id_cpm', $data->id)->first();

            ProsesData::where('id_cpm', $data->id)->update([
                'early_finish' => $this->getEarlyFinish($proses_data->id)
            ]);
        }

        $data_proses = ProsesData::orderBy('id', 'desc')->get();

        foreach($data_proses as $data){
            ProsesData::where('id', $data->id)->update([
                'late_start' => $this->getLateStart($data->id)
            ]);
        }
        foreach($data_proses as $data){
            ProsesData::where('id', $data->id)->update([
                'late_finish' => $this->getLateFinish($data->id)
            ]);
        }
    }

    public function getPenerus($id){
        $result = "";
        $cpm = DataCpm::where('id', $id)->first();
        $penerus = DataCpm::where('pendahulu', 'like', '%' . $cpm->kode . '%')->get();

        if($penerus->count() != 0){
            foreach($penerus as $item){
                $result .= $item->kode.',';
            }
        }
        else{
            $result = "Selesaii";
        }
        

        return substr_replace($result, "", -1);
    }

    public function resetProses(){
        ProsesData::truncate();

        $this->emit('showAlert', ['msg' => 'Proses berhasil dihapus']);
    }

    public function earlyStart($id){
        $data_cpm = DataCpm::where('id', $id)->first();
        $pendahulu_null = $data_cpm->pendahulu;
        $pendahulu = explode(',', $data_cpm->pendahulu);
        
        if($pendahulu_null == null){
            $early_start = 0;
        }
        else{
            if(count($pendahulu) == 1){
                $pendahulu_cpm = DataCpm::where('kode', $data_cpm->pendahulu)->first();
                $pendahulu_data = ProsesData::where('kode', $data_cpm->pendahulu)->first();
    
                $early_start = $pendahulu_cpm->durasi + $pendahulu_data->early_start;
            }
            else{
                $a = 0;
                foreach($pendahulu as $item){
                    $pendahulu_cpm = DataCpm::where('kode', $item)->first();
                    $pendahulu_data = ProsesData::where('kode', $item)->first();

                    $result = $pendahulu_cpm->durasi + $pendahulu_data->early_start;

                    if($a < $result){
                        $a = $result;
                    }

                    else{
                        $a = $a;
                    }
                }

                $early_start = $a;
            }
        }

        return $early_start;
    }

    public function getEarlyFinish($id){
        $data = ProsesData::where('id', $id)->first();
        
        $early_finish = $data->cpm->durasi + $data->early_start;

        return $early_finish;
    }

    public function getLateStart($id){
        $data_proses = ProsesData::where('id', $id)->first();


        $pendahulu = explode(',', $data_proses->penerus);

        if($data_proses->penerus == "Selesai"){
            $pendahulu_data = ProsesData::where('id', $id)->first();

            $late_start = $pendahulu_data->early_finish - $pendahulu_data->cpm->durasi;
        }
        else{
            if(count($pendahulu) == 1){
                $pendahulu_penerus = ProsesData::where('kode', $data_proses->penerus)->first();
    
                $late_start = $pendahulu_penerus->late_start - $data_proses->cpm->durasi;
            }
            else{
                $a = 0;
                foreach($pendahulu as $item){
                    $pendahulu_data = ProsesData::where('kode', $item)->first();

                    $result =  $pendahulu_data->late_start - $data_proses->cpm->durasi;


                    if ($result == 0){
                        $a = 0;
                        break;
                    }
                    else{
                        if($a == 0){
                            $a = $result;
                        }
                        else if($result < $a){
                            $a = $result;
                        }
                        else{
                            $a = $a;
                        }
                        
                    }
                }

                $late_start = $a;
            }
        }

        return $late_start;
    }

    public function getLateFinish($id){
        $data = ProsesData::where('id', $id)->first();
        
        $late_finish = $data->cpm->durasi + $data->late_start;

        return $late_finish;
    }
}
