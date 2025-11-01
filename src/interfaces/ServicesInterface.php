<?php

namespace Najla\Interfaces;

/**
 * Interface ServicesInterface
 * 
 * This interface provides utility functions for the application.
 * 
 * @package     Najla\Interfaces 
 * @author      Nabil Makhnouq
 * @version     1.0.0
 * @since       File available since Release 1.0.0
 */
interface ServicesInterface
{
    /**
     * Get the instance
     * 
     * This method:
     * - Returns the instance
     * 
     * @return object The instance
     */
    public static function getInstance(): object;
} 