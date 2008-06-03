#! /usr/bin/perl -w

use DBI;
use Switch;
use Esprit;
use Getopt::Long qw(:config bundling);

# initialisation des options
%opts = (
	 verbose => 0,
	 debug => 0,
		 user => "root",
		 password => "",
	);

# lecture des options
GetOptions(\%opts,
	   "man",
	   "help|h",
	   "verbose|v+",
	   "debug|D+",
		   "password|p",
		   "user|u=s",
		   "type|t=s",
		   "id|i=s",
	  );

# options d'aide grâce à Pod::Usage
use Pod::Usage;
pod2usage(-verbose => 2) if $opts{man};
pod2usage(-verbose => 0) if $opts{help};

require Data::Dumper if $opts{debug};
$Data::Dumper::Indent = 1;

pod2usage(-verbose => 0) if (@ARGV != 1);
pod2usage(-verbose => 0) unless $opts{type} and $opts{id};

# init MySQL
if ($opts{password} == 1) {
	print STDERR "Mot de passe : ";
	if (eval "no warnings 'all'; require Term::ReadKey;") {
		Term::ReadKey::ReadMode('noecho');
		$opts{password} = Term::ReadKey::ReadLine(0);
	} else { # à défaut de Term::ReadKey
		system ("stty -echo");
		$opts{password}=<STDIN>;
		system ("stty echo");
	}
	chomp($opts{password});
}
$opts{database} = shift or die "Base de données ?\n";
my $db = DBI->connect("DBI:mysql:$opts{database}",$opts{user},$opts{password},
					  { RaiseError => 1, PrintError=>1, AutoCommit => 1 } );
die "Erreur avec la base $opts{database}.\n" unless $db;
my $esprit = Esprit->new($db);

my $dir;
switch ($opts{type}) {
	case /^rubrique$/i { $opts{type}="Module_Rubrique"; $dir = "Rubrique_$opts{id}"; }
	case /^activ$/i {  $opts{type}="Activ"; $dir = "Activ_$opts{id}"; }
	case /^sous-?activ$/i { $opts{type}="SousActiv"; $dir = "SousActiv_$opts{id}"; }
}
mkdir($dir,0777);
chmod(0777,$dir); # pour que l'utilisateur mysql ait un accès
chdir($dir);

$esprit->selectBranch($opts{type},$opts{id});
$esprit->dump();


#################################################################


__END__

=head1 NAME

esprit_dump.pl

=head1 SYNOPSIS

esprit_dump.pl [--user=|-u user] [-p] --type=I<type> --id=I<id> database

Crée un répertoire, par exemple nommé Rubrique_I<id> qui contient les dumps de cette rubrique.
Attention aux droits MySQL sur les accès fichiers !

=head1 OPTIONS

=over 8

=item B<-h, --help>

Affiche une page d'aide abrégée.

=item B<--id, -i> id

L'B<id> de la racine du dump.

=item B<--man>

Affiche cette page de man.

=item B<--password, -p>

Demander le mot de passe.

=item B<--user, -u> user

L'utilisateur MySQL. Par défaut, root.

=item B<--type, -t> type

Le niveau où se situe la racine : rubrique, activ, sousactiv.

=item B<-v, --verbose>

Incrémente la verbosité.

=back

=cut
