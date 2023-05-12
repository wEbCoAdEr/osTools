# OS Tools CLI
This is a simple Command Line Interface (CLI) program that provides basic system information and some tools for Linux-based operating systems. The program is written in PHP.

## Requirements

-   PHP 7.2 or later
-   Linux-based operating system

## Installation

1.  Clone or download the repository.
2.  Make sure you have PHP installed on your system.
3.  Open your terminal and navigate to the directory where the repository is located.
4.  Run the program by entering `php ostools.php`.

## Features

The program currently supports the following commands:

-   `help`: Displays a list of available commands and their parameters.
-   `info`: Shows information about the system's memory and CPU.
-   `list processes [n]`: Displays a list of running processes owned by the current user. The optional `n` parameter specifies the number of processes to display (default is 20).
-   `list users [n]`: Displays a list of system users. The optional `n` parameter specifies the number of users to display (default is 10).
-   `exit`: Exits the program.

## License

This program is open-source and available under the [MIT License](https://github.com/wEbCoAdEr/osTools/blob/main/LICENSE).
