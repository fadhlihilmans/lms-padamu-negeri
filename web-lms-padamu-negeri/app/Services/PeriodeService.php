<?php

namespace App\Services;

use App\Models\PeriodeAjaran;

class PeriodeService
{
    public function getSelected(): ?PeriodeAjaran
    {
        $id = session('periode_id_selected');

        if ($id) {
            $periode = PeriodeAjaran::find($id);
            if ($periode) {
                return $periode;
            }
            session()->forget('periode_id_selected');
        }

        return PeriodeAjaran::where('is_aktif', true)->first();
    }

    public function isReadOnlyMode(): bool
    {
        $periode = $this->getSelected();
        return $periode !== null && ! $periode->is_aktif;
    }

    public function setPeriod(int $id): void
    {
        session(['periode_id_selected' => $id]);
    }

    public function resetToActive(): void
    {
        session()->forget('periode_id_selected');
    }
}
