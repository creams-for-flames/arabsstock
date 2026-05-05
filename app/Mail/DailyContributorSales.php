<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DailyContributorSales extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    protected $sales;
    protected $email;
    protected $total_profit_value;
    protected $contributor_name;
    public function __construct($sales,$email,$total_profit_value,$contributor_name)
    {
        $this->sales = $sales;
        $this->email = $email;
        $this->total_profit_value = $total_profit_value;
        $this->contributor_name = $contributor_name;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->to([$this->email])
            ->subject('Daily Contributor Sales')
            ->markdown('emails.daily_contributor_sales')
            ->with(['sales'=>$this->sales,'total_profit_value'=>$this->total_profit_value,'contributor_name'=>$this->contributor_name]);
    }
}
