#! /usr/bin/perl -w
# use File::Find;
use Getopt::Std;


sub usage()
    {
        print STDERR << "EOF";

    $0 counts PHP lines in mixed files.

    usage: $0 [-f file]

     -h        : this (help) message
     -c        : comments
     -f file   : code file to be parsed

    example: $0 -f class_include.php

EOF
    exit;
    }

my $presentation="Counts PHP lines, comments...\n";
#my $debug=$verbose=0;
my $Filename;
my %options=();
my $line=$phpline=0;
my $com_FP_line=$com_double_slash_line=0;
my $b_inPHP=0;

getopts("hcf:",\%options);

if (defined $options{h} )
    { usage(); }

if (defined $options{f} )
    { $Filename= $options{f}; }
  else
    { die "file is mandatory.\n"; }



print "Unprocessed by Getopt::Std:\n" if $ARGV[0];
foreach (@ARGV) 
{
  print "$_\n";

}


open FILE,"<", $Filename or die "Unable to open $Filename (read mode).\n";

while (<FILE>) 
{ $line++;
  if (/^<\?php/)
	{ $b_inPHP=1;
	  $phpline++;
        }
  elsif (/^\?>/)
        { $b_inPHP=0;
	  $phpline++;
      }
  elsif ($b_inPHP)
        { $phpline++;
	  if ( /^\/\*/ || /\*\*/ || /^\*\// )
	     {$com_FP_line++;
             }
	   if ( /\/\//  )
	     {$com_double_slash_line++;
             }
	  
      }
	
} # end while(<FILE>)


print "$Filename  P=$phpline  T=$line";
print "    CFP=$com_FP_line  C//=$com_double_slash_line" if (defined $options{c});


print "\n";

