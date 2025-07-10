<?php

namespace Najla\Core\Interface;

interface ExceptionInterface
{
    public function getLogLevel(): string;
    public function getContext(): array;
    public function getLogFile(): string;
} 
