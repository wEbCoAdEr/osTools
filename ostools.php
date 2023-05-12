<?php

/*
MIT License

Copyright (c) 2023 wEbCoAdEr

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
*/

//Disabled PHP Error Reporting
error_reporting(0);

//Print Tool Branding
echo <<<EOL
  ___  ____      _____           _     
 / _ \/ ___|    |_   _|__   ___ | |___ 
| | | \___ \ _____| |/ _ \ / _ \| / __|
| |_| |___) |_____| | (_) | (_) | \__ \
 \___/|____/      |_|\___/ \___/|_|___/
                                      
EOL;

//Shows warning message if Linux OS is not detected and exit the script
if(PHP_OS !== 'Linux'){
    echo "\nThis tool only supports linux-based operating systems. Please use it on Linux & say no to Windows...";
    exit();
}

define('CURRENT_USER', get_current_user());

echo "\nCURRENT USER: " . CURRENT_USER;
echo "\nSYSTEM INFO: " . php_uname();
echo "\nOPERATING SYSTEM: " . PHP_OS;
echo "\nPROCESSOR: " . getCpuInfo()['NAME'];
echo "\nTOTAL MEMORY: " . ((getAvailableMemory() / 1024) / 1024) , " GB";
echo "\nTOTAL STORAGE: " . (((disk_total_space("/") / 1024) / 1024) / 1024) , " GB";

//Print line gap after branding sectionlist-processes
echo "\n \n";

//Declare program functions
function processHelpCommand()
{

    echo <<<EOL
"info" command shows the information of memory and cpu. Parameters are memory and cpu.
"list" command shows the processes list and users list. Parameters are processes and users. 
"exit" command exit from the os tools 
EOL;

    echo "\n";

}

function getAvailableMemory(){
    $fh = fopen('/proc/meminfo','r');
    $mem = 0;
    while ($line = fgets($fh)) {
        $pieces = array();
        if (preg_match('/^MemTotal:\s+(\d+)\skB$/', $line, $pieces)) {
            $mem = $pieces[1];
            break;
        }
    }
    fclose($fh);

    return $mem;

}

function getSystemMemInfo()
{
    $data = explode("\n", file_get_contents("/proc/meminfo"));
    $meminfo = array();
    foreach ($data as $line) {
        list($key, $val) = explode(":", $line);
        $meminfo[$key] = trim($val);
    }
    return $meminfo;
}

function getCpuInfo(){
    $cpuget = file('/proc/cpuinfo');
    $cpu["NAME"]    =   explode(':', str_replace("           ", " ",substr(str_replace("model name   : ", "", $cpuget[4]),0,-1)))[1];
    $cpu["CORES"]   =   explode(':', substr(str_replace("cpu cores   : ", "", $cpuget[12]),0,-1))[1];
    $cpu["CACHE"]   =   explode(':', substr(str_replace("cache size  : ", "", $cpuget[8]),0,-1))[1];
    return $cpu;
}

function processListCommand($operation = 'processes', $numberOfProcesses)
{
    if (is_int($numberOfProcesses) && $numberOfProcesses > 0) {

        $output = "";

        switch ($operation) {
            case 'processes':
                $count = 0;
                $execstring = "ps -f -u " . CURRENT_USER . " 2>&1";
                break;
            case 'users':
                $count = 1;
                $execstring = "cat /etc/passwd";
                break;
            default:
                $execstring = "ps -f -u " . CURRENT_USER . " 2>&1";
        }

        exec($execstring, $output);

        echo "\n";

        foreach ($output as $result) {
            if ($count <= $numberOfProcesses) {
                echo $result . "\n \n";
            }
            $count++;
        }
    } else {
        echo "Second parameter should be a valid positive number which describes how much processes should be output. \n";
    }
}

//Start infinite while loop to keep the program running
while (true) {


    echo 'ostools>> ';

    $commandArray = ['exit', 'help', 'list', 'info'];

    $input = trim(fgets(STDIN, 2048));

    $inputCommandArr = explode(' ', $input);

    $baseCommand = $inputCommandArr[0];

    if (in_array($baseCommand, $commandArray)) {

        //Process if recognized command is found

        switch ($baseCommand) {
            case $commandArray[0]:
                return false;
            case $commandArray[1]:
                processHelpCommand();
                break;
            case $commandArray[2]:

                $listSubCommandsArray = ['processes', 'users'];

                $subCommand = $inputCommandArr[1];

                if (in_array($subCommand, $listSubCommandsArray)) {

                    switch ($subCommand) {
                        case 'processes':
                            $numberOfProcesses = ($inputCommandArr[2] > 0) ? (int)$inputCommandArr[2] : 20;
                            processListCommand('processes', $numberOfProcesses);
                            break;
                        case 'users':
                            $numberOfUsers = ($inputCommandArr[2] > 0) ? (int)$inputCommandArr[2] : 10;
                            processListCommand('users', $numberOfUsers);
                            break;
                    }
                } else {
                    echo "list command requires additional parameter. Avaialable parameters are \n \n";

                    $count = 1;
                    foreach ($listSubCommandsArray as $listSubCommand) {
                        echo "[$count]" . $listSubCommand . "\n";
                        $count++;
                    }
                    echo "\nExample: list processes \n \n";
                }
                break;
            case $commandArray[3]:

                $listSubCommandsArray = ['memory', 'cpu'];

                $subCommand = $inputCommandArr[1];

                if (in_array($subCommand, $listSubCommandsArray)) {

                    switch($subCommand){
                        case 'memory':
                            $memory_info = getSystemMemInfo();

                            foreach($memory_info as $key => $value){
                                if(!empty($key) && !empty($value)){
                                    echo $key . " : " . $value . "\n";
                                }
                            }
                            break;
                        case 'cpu':
                            $cpu_info = getCpuInfo();

                            foreach($cpu_info as $key => $value){
                                if(!empty($key) && !empty($value)){
                                    echo $key . " : " . $value . "\n";
                                }
                            }
                            break;

                    }
                }else {
                    echo "info command requires additional parameter. Avaialable parameters are \n \n";

                    $count = 1;
                    foreach ($listSubCommandsArray as $listSubCommand) {
                        echo "[$count]" . $listSubCommand . "\n";
                        $count++;
                    }
                    echo "\nExample: info memory \n \n";
                }
                break;
        }
    } else {
        //Output error message if recognized command is not found
        echo 'NO COMMAND FOUND "' . $input . '". Please run help command to list the available commands.' . "\n";
    }
}
