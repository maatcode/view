<?php

namespace Maatcode\View\Exception;

interface ExceptionInterface
{
    /* Protected methods inherited from Exception class */
    public function getMessage();                 // Exception message
    public function getCode();                    // User-defined Exception code
    public function getFile();                    // Source filename
    public function getLine();                    // Source line
    public function getTrace();                   // An array of the backtrace()
    public function getTraceAsString();           // Formatted string of trace

    /* Overrideable methods inherited from Exception class */
    public function __toString();                 // Formatted string for display
    public function __construct($message = null, $code = 0);
}
