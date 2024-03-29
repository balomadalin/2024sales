<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;
use App\Models\Invoice;


class Client extends Model
{
    use HasFactory;

    /**
     * The projects this client ordered.
     */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }
    public function invoice(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }


    /**
     * Number of hours worked for this client
     */
    public function getHoursAttribute()
    {
        $hours = 0;
        foreach ($this->projects as $project) {
            foreach ($project->invoices as $invoice) {
                foreach ($invoice->positions as $position) {
                    $hours += $position->duration;
                }
            }
        }
        return $hours;
    }

    /**
     * Net amount earned by this client
     */
    public function getNetAttribute()
    {
        $net = 0;
        foreach ($this->projects as $project) {
            foreach ($project->invoices as $invoice) {
                $net += $invoice->net;
            }
        }
        return $net;
    }

    /**
     * Number of days this client takse to pay bills on average
     */
    public function getAvgPaymentDelayAttribute()
    {
        $days = [];
        foreach ($this->projects as $project) {
            foreach ($project->invoices as $invoice) {
                if ($invoice->invoiced_at && $invoice->paid_at) {
                    $days[] = Carbon::parse($invoice->invoiced_at)->floatDiffInDays($invoice->paid_at);
                }
            }
        }
        return count($days) ? array_sum($days)/count($days) : 0;
    }
}
