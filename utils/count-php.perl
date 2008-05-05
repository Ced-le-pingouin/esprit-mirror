#! /usr/bin/perl -w
# use File::Find;
use Getopt::Std;

my %c_block=(
	     "outside" => 0,
	     "first_line" => 1,
	     "inside" =>2 
	     );

sub usage()
    {
        print STDERR << "EOF";

    $0 counts PHP lines in mixed files.

    usage: $0 [-f file]

     -h        : this (help) message
     -c        : + display comments lines
     -s        : score : display a single count of "effective" PHP code
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
my $com_OEC_line=$com_double_slash_line=$com_doxy_line=$com_c_block_line=0;
my $b_inPHP=0;
my $t_c_block=$c_block{"outside"};

getopts("shcf:",\%options);

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

  if (/^<\?php.*?\?>\s*$/)  # one single line <?php ....  ?>
	{ $phpline++;
        }
  elsif (/^<\?php/)         # entering PHP
	{ $b_inPHP=1;
	  $phpline++;
        }
  elsif (/^\?>/)            # and exit
        { $b_inPHP=0;
	  $phpline++;
      }
  elsif ($b_inPHP)          # inside : seeking for comments...
        { $phpline++;

	  if ( /^\/\*\*/ || /^ \* / || /^ \*\// )   # Doxygen style
	     {$com_doxy_line++;
             }
	  elsif ( /^\/\*[^*].*?\*\s*$/ )  # single line /* foo bar */
	     {$com_c_block_line++;
             }	 
	  elsif ( /^\/\*[^*]/ )  # Entering /* block
	     {$com_c_block_line++;
	      $t_c_block=$c_block{"first_line"};
             }	  
	  elsif ( /^\*\// )      # Exit block */
	     {$com_c_block_line++;
	      $t_c_block=$c_block{"outside"};
             }	  
	  elsif ( /^\*\*/ )  # Old Esprit Copyright style
	     {$com_OEC_line++;
	      if ($t_c_block == $c_block{"first_line"})
	         { $com_OEC_line+=2;
		   $com_c_block_line-=2;
		   $t_c_block = $c_block{"inside"}
	         }
             }
	  elsif ( /^\/\//  )                           # C++ style (//)
	     {$com_double_slash_line++;
             }
	  
      }
	
} # end while(<FILE>)

$score = $phpline - 
    ($com_doxy_line + $com_OEC_line + $com_double_slash_line + $com_c_block_line);
printf("%4d   ",$score) if (defined $options{s}); 
print "$Filename  ";
print "P=$phpline  T=$line" if (! defined $options{s});
print "    Coec=$com_OEC_line  C/*=$com_c_block_line ".
      "C//=$com_double_slash_line  Cdoxy=$com_doxy_line" if (defined $options{c});

print "\n";

