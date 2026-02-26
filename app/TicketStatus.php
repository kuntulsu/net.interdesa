<?php

namespace App;

enum TicketStatus: string {
    case Waiting = 'waiting';
    case Process = 'process';
    case Completed = 'completed';
}