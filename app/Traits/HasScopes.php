<?php

namespace App\Traits;

trait HasScopes {

    public function scopeRecentlySold($query, $days)
    {
        $days = $days ?? 90;
        $now = \Carbon\Carbon::now();
        $daysAgo = $now->copy()->subDays($days);
        return $query->where('sold_date', '>=', $daysAgo);
    }

    public function scopeNewListings($query, $days)
    {
        $days = $days ?? 10;
        $now = \Carbon\Carbon::now();
        $daysAgo = $now->copy()->subDays($days);
        return $query->where('list_date', '>=', $daysAgo);
    }

    public function scopeBy($query, $officeCode)
    {
        return $query->where('lo_code', $officeCode)
            ->orWhere('co_lo_code', $officeCode)
            ->orWhere('so_code', $officeCode);
    }

    // public function scopeRecentlySoldBy($query, $officeCode)
    // {
    //     $oneYearAgo = \Carbon\Carbon::now()->copy()->subYearNoOverflow();
    //     return $query->where('lo_code', $officeCode)
    //                  ->orWhere('co_lo_code', $officeCode)
    //                  ->orWhere('so_code', $officeCode)
    //         ->where('sold_date', '>=', $oneYearAgo)
    //         ->whereNotNull('sold_date');
    // }

    public function scopeRecentlySoldBy($query, $officeCode)
    {
        $oneYearAgo = \Carbon\Carbon::now()->copy()->subYearNoOverflow();
        return $query->where(function ($q) use ($officeCode) {
            return $q->where('lo_code', $officeCode)
                ->orWhere('co_lo_code', $officeCode)
                ->orWhere('so_code', $officeCode);
            })
            ->where('sold_date', '>=', $oneYearAgo)
            ->whereNotNull('sold_date');
    }

    public function scopeWaterFront($query)
    {
        return $query->where('ftr_waterfront', '!=', '');
    }

    public function scopeForclosures($query)
    {
        return $query->where('ftr_ownership', 'like', '%Bankruptcy%')
            ->orWhere('ftr_ownership', 'like', '%Foreclosure%')
            ->orWhere('ftr_ownership', 'like', '%Short Sale%')
            ->orWhere('ftr_ownership', 'like', '%Real Estate Owned%');
    }

    public function scopeContingentOrPending($query)
    {
        return $query->where('status', 'Contingent')->orWhere('status', 'Pending');
    }

    public function scopeExcludeAreas($query, $areas)
    {
        return $query->whereNotIn('area', $areas);
    }
}