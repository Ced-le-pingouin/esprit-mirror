#! /usr/bin/perl -w

use Getopt::Long qw(:config bundling);
use locale;

use Spreadsheet::ParseExcel::Simple;
# DEBIAN : apt-get install libspreadsheet-parseexcel-simple-perl
# ATTENTION au bug dans la 0.2603 (debian-etch) qui produit des avertissements inoffensifs
# dans /usr/share/perl5/Spreadsheet/ParseExcel.pm, ligne 1808-1809
#		substr($sWk, 3, 1) &=  pack('C', unpack("C",substr($sWk, 3, 1)) & 0xFC);
#		substr($lWk, 0, 1) &=  pack('C', unpack("C",substr($lWk, 0, 1)) & 0xFC);
# dans /usr/share/perl5/Spreadsheet/ParseExcel/FmtDefault.pm, ligne 68
#		return pack('U*', unpack('n*', $sTxt));

my @emailHeader = (
		'Reply-To: cellule.tice@u-grenoble3.fr'
			   );
my $defaultEmail=<<END;
Bonjour \%s \%s

Vous avez été inscrit à la plate-forme d'apprentissage Esprit-Flodi :
http://flodi.grenet.fr/

Formation : <Form>
Cours : <Mod>

Vous pouvez vous connecter en entrant ces coordonnées dans la page
d'accueil :

Pseudo : \%s
Mot de passe : \%s

Bonne journée
-=-
Cellule TICE
Université Stendhal - Langues
END

##############

my $commandline = join("\n\t", @ARGV);

# initialisation des options
%opts = (
	 verbose => 0,
	 debug => 0,
	 oldpassword => 0,
	);

# lecture des options
GetOptions(\%opts,
	   "man",
	   "help|h",
	   "verbose|v+",
	   "debug|D+",
		   "oldpassword|old-password|o",
		   "formation|f=s",
		   "cours|c|module=s",
		   "unique|u=s",
		   "mail|m=s",
	  );
$opts{verbose} += $opts{debug};

# options d'aide grâce à Pod::Usage
use Pod::Usage;
pod2usage(-verbose => 2) if $opts{man};
pod2usage(-verbose => 0) if $opts{help};
pod2usage(-verbose => 0) unless @ARGV;

print "/* esprit_inscr.pl\n   Options :\n\t$commandline\n*/\n\n";
print "SET NAMES 'utf8';\n";

foreach $excelfile (@ARGV) {
	my @users;
	print "\n/* ***** FILE : $excelfile ***** */\n\n";
	warn "/* ***** FILE : $excelfile ***** */\n" if $opts{verbose};
	my $xls = Spreadsheet::ParseExcel::Simple->read($excelfile);
	die "Ouverture impossible !" unless defined $xls;
	foreach my $sheet ($xls->sheets) {
		next unless $sheet->has_data;
		my @cols = $sheet->next_row;
		warn "COLS : ".join(',',@cols)."\n" if $opts{debug};
		while ($sheet->has_data) {
			my @data = $sheet->next_row;
			warn 'ROW :  '.join(',',@data)."\n" if $opts{debug};
			die "Incomplet ($excelfile) : ".join(';',@data)."--".scalar(@data)."--".scalar(@cols)
				unless scalar(@data)==scalar(@cols);
			push @users, parseRow(\@data,@cols);
		}
	}
	output(\@users);
}


#################################################################

###################
sub output {
	my $ainscrire = shift;
	foreach my $u (@$ainscrire) {
		print "INSERT INTO Personne (", join(',',keys(%$u)),
			") VALUES (", join(',',escape(values(%$u))), ");\n";
		if ($opts{formation}) {
			print 'SET @userID:=LAST_INSERT_ID();'."\n";
			print "INSERT INTO Formation_Inscrit (IdForm,IdPers)",
				" VALUES ",
					join(', ', map { "($_,\@userID)" } split(/,/,$opts{formation})),
						" ;\n";
		}
		if ($opts{cours}) {
			print "INSERT INTO Module_Inscrit (IdMod,IdPers)",
				" VALUES ",
					join(', ', map { "($_,\@userID)" } split(/,/,$opts{cours})),
						" ;\n";
		}
		print "\n";
	}

	if ($opts{unique}) {
		open UNIQUE, ">>", $opts{unique}
			or die "Pb de fichier : $!";
		foreach my $u (@$ainscrire) {
			print UNIQUE "SELECT * FROM Personne WHERE Pseudo='$u->{Pseudo}';\n";
		}
		print UNIQUE "\n";
		close UNIQUE;
	}

	if ($opts{mail}) {
		open MAIL, ">>", $opts{mail}
			or die "Pb de fichier : $!";
		my $mail = "echo \"$defaultEmail\" | mail -s \"Inscription à Esprit\" ".
			join(' ', map {"-a \"$_\""} @emailHeader).
				" \%s\n";
		foreach my $u (@$ainscrire) {
			if (not exists $u->{Email} or !$u->{Email}) {
				print MAIL "\n",'#'x50,
					"\necho \"ATTENTION : $u->{Prenom} $u->{Nom} n'a pas d'e-mail\"\n",
						'#'x50, "\n\n";
				next;
			}
			print MAIL "##########\n";
			$u->{Mdp} =~ /PASSWORD\('(.+?)'\)/;
			printf MAIL $mail,
				$u->{Prenom}, $u->{Nom},
					$u->{Pseudo}, $1,
						$u->{Email};
		}
		print MAIL "\n";
		close MAIL;
	}
}

###################
sub escape {
	my @escaped;
	foreach my $val (@_) {
		if ($val =~ m/PASSWORD\(/) {
			push @escaped, $val;
		} else {
			push @escaped, "'$val'";
		}
	}
	return @escaped;
}

###################
sub parseRow {
	my %required = qw(nom Nom prenom Prenom pseudo Pseudo mdp Mdp);
	my %optional = qw(datenaiss DateNaiss sexe Sexe email Email urlperso Urlperso adresse Adresse);
	my ($data,@titles) = @_;
	foreach my $t (@titles) {
		$t = lc($t);
		$t =~ tr/àâéèêëîïôùûç/aaeeeeiiouuc/;
		if (exists $required{$t}) {
			$t = $required{$t};
			delete $required{$t};
		} elsif (exists $optional{$t}) {
			$t = $optional{$t};
			delete $optional{$t};
		} else {
			warn "Colonne inconnue ($t) in @titles\n" unless $warned;
			$warned++;
		}
	}
	my %result;
	foreach my $d (@$data) {
		my $col = shift @titles;
		my $val = $d;
		$val =~ s/'/\\'/g;
		$val = uc($val) if ($col eq 'Nom');
		if ($col eq 'Mdp') {
			if ($opts{oldpassword}) {
				$result{$col} = "OLD_PASSWORD('$val')";
			} else {
				$result{$col} = "PASSWORD('$val')";
			}
		} elsif ($val =~ /^null$/i) {
			# Do no show these entries
			# $result{$col} = 'NULL';
		} else {
			$result{$col} = $val;
		}
	}
	foreach my $t (values %required) {
		if (!$result{$t} or $result{$t}=~/^null$/i) {
			warn "Non pris en compte (incomplet) : $result{Nom}, $result{Prenom}\n"
				if exists $result{Nom} and $result{Nom};
			return ();
		}
	}
	return { %result };
}


#################################################################


__END__

=head1 NAME

esprit-inscr.pl

=head1 SYNOPSIS

esprit_inscr.pl --man

esprit_inscr.pl [options] [fichiers.xls]

Affiche le SQL qui inscrit dans Esprit les personnes décrites dans des fichiers Excel.
Il est recommandé de tester l'unicité avec l'option B<--unique>.

=head1 OPTIONS

=over 8

=item B<-c, --cours> IdMod1,IdMod2,...

Inscrit les personnes aux cours ayant cet Id (table Module). La syntaxe B<--module>=... est possible.

=item B<-f, --formation> IdForm1,IdForm2,...

Inscrit les personnes aux formations ayant cet Id (table Formation).

=item B<-h, --help>

Affiche une page d'aide abrégée.

=item B<--man>

Affiche cette page de man.

=item B<-m, --mail> I<fichier>

Ecrit dans le fichier les commandes shell qui envoient un mail à chaque inscrit.
Il faut ensuite remplacer <Form> et <Mod>, par exemple avec :

perl -i -pe 's/<Form>/Japonais/g; s/<Mod>/LANSAD & F Continue/;' I<fichier>

=item B<-o, --oldpassword>

Utilise OLD_PASSWORD() au lieu de PASSWORD().

=item B<-u, --unique> I<fichier>

Ajoute dans le fichier les instructions SQL pour tester l'unicité des pseudos, par exemple avec :

./esprit_inscr.pl inscrits.xls -f 2 -m 44,46 --unique=unique.sql

mysql espritDB < unique.sql

=item B<-v, --verbose>

Incrémente la verbosité.

=back

=head1 EXEMPLE

=over 8

./esprit_inscr.pl --formation 163 --cours 644 --mail espr-inscr-mail.sh --unique espr-inscr-uniq.sql *.xls > espr-inscr.sql

En zsh :

for file in *.xls; do
  ./esprit_inscr.pl -f 163 -c 644 -m mail_$i:r_.sh -u uniq_$i:r.sh $i > espr-inscr-$i.sql
done

=back

=cut
