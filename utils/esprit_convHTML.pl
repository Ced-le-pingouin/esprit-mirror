#! /usr/bin/perl -w

use DBI;
use Getopt::Long qw(:config bundling);
#use locale;

##############

# Pour chaque table, les champs identifiants et les colonnes à convertir.
%tables = (
		   Formulaire => { id => ['IdFormul','IdPers'], cols => ['Commentaire','Titre'] },
		   FormulaireComplete_Evaluation => { id => ['IdFCSousActiv','IdPers'] , cols => ['CommentaireEval'] },
		   QTexteLong => { id => ['IdObjFormul'], cols => ['EnonQTL'] },
		   QTexteCourt => { id => ['IdObjFormul'], cols => ['EnonQTC', 'TxtAvQTC', 'TxtApQTC'] },
		   QNombre => { id => ['IdObjFormul'], cols => ['EnonQN', 'TxtAvQN', 'TxtApQN'] },
		   QListeDeroul => { id => ['IdObjFormul'], cols => ['EnonQLD', 'TxtAvQLD', 'TxtApQLD'] },
		   QRadio => { id => ['IdObjFormul'], cols => ['EnonQR', 'TxtAvQR', 'TxtApQR'] },
		   QCocher => { id => ['IdObjFormul'], cols => ['EnonQC', 'TxtAvQC', 'TxtApQC'] },
		   MPTexte => { id => ['IdObjFormul'], cols => ['TexteMPT'] },
		   PropositionReponse => { id => ['IdPropRep'], cols => ['TextePropRep','FeedbackPropRep'] },

		   Ressource => { id => ['IdRes'], cols => ['DescrRes'] },
		   Ressource_SousActiv_Evaluation => { id => ['IdResSousActiv','IdPers'], cols => ['CommentaireEval'] },

		   Accueil => { id => ['Id'], cols => ['Texte'] },
		   SousActiv => { id => ['IdSousActiv'], cols => ['DescrSousActiv'] },
		   Activ => { id => ['IdActiv'], cols => ['DescrActiv'] },
		   Module => { id => ['IdMod'], cols => ['DescrMod'] },
		   Module_Rubrique => { id => ['IdRubrique'], cols => ['DescrRubrique'] },
		   Formation => { id => ['IdForm'], cols => ['DescrForm'] },
		   MessageForum => { id => ['IdMessageForum'], cols => ['TexteMessageForum'] },
#		    => { id => [], cols => [] },
# Glossaire > TexteGlossaire ???
);

##############

# initialisation des options
%opts = (
	 verbose => 0,
	 debug => 0,
		 user => "esprit-admin",
		 password => 0,
	);

# lecture des options
GetOptions(\%opts,
	   "man",
	   "help|h",
	   "verbose|v+",
	   "debug|D+",
		   "password|p",
		   "user|u=s",
	  );
$opts{verbose} += $opts{debug};

require Data::Dumper if $opts{debug};
$Data::Dumper::Indent = 1;

# options d'aide grâce à Pod::Usage
use Pod::Usage;
pod2usage(-verbose => 2) if $opts{man};
pod2usage(-verbose => 0) if $opts{help};
pod2usage(-verbose => 0) unless @ARGV==1;

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
} else {
	$opts{password} = '';
}
$opts{database} = shift or die "Base de données ?\n";
my $db = DBI->connect("DBI:mysql:$opts{database}",$opts{user},$opts{password},
					  { RaiseError => 1, PrintError=>1, AutoCommit => 1 } );
die "Erreur avec la base $opts{database}.\n" unless $db;
$db->do("SET NAMES 'utf8';");
## $db->{TraceLevel} = 1 if $opts{debug};


foreach my $table (keys %tables) {
	my @idcols = @{ $tables{$table}{id} };
	my @cols = @{ $tables{$table}{cols} };

	my $rows = $db->selectall_arrayref("SELECT "
									   .join(", ",@idcols,@cols)
									   ." FROM $table"
								   );

	my @set;
	push(@set, "$_ = ?") foreach (@cols);
	my @where;
	push(@where, "$_ = ?") foreach (@idcols);
	my $update = $db->prepare("UPDATE $table SET ".join(" , ",@set)
							  ." WHERE ".join(" AND ",@where));
	foreach my $row (@$rows) {
		next unless @$row;
		warn "ROW : " . Data::Dumper::Dumper($row) if $opts{debug};
		warn("UPDATE $table SET ".join(", ",@set)." WHERE ".join(" AND ",@where)
			."\n\twith : ".join(' - ',convert2HTML($#idcols,$row))."\n")
				if $opts{debug};
		$update->execute(convert2HTML($#idcols,$row));
	}
}


#################################################################

sub convert2HTML {
	my $idnum = shift;
	my $row = shift;
	my @res;
	for(my $i=$idnum+1; $i<@$row; $i++) {
		$_ = $row->[$i];

		if ($_ and length($_)) {
			s/\[(h1|h2|h3|h4|h5|h6|b|u|i|s|blockquote|center)\]/<$1>/g;
			s/\[\/(h1|h2|h3|h4|h5|h6|b|u|i|s|blockquote|center)\]/<\/$1>/g;
			s/\[tab\]/<blockquote]>/g;
			s/\[\/tab\]/<\/blockquote]>/g;
			s{\[n\]}{<span style="font-weight: normal;">}g;
			s{\[/n\]}{</span>}g;

			s{\[(https?://[^\s\]]+)\]}{<a href="$1" target="_blank" onfocus="blur()">$1</a>}g;
			s{\[(https?://[^\s\]]+) ([^\]]+)\]}{<a href="$1" target="_blank" onfocus="blur()">$2</a>}g;
			s{\[mailto:\s?([^\]]+)\]}{<a href="mailto:$1" title="Envoyer un e-mail" onfocus="blur()">$1</a>}g;

			s{\[l\]}{<div style="text-align:left;">}g;
			s{\[c\]}{<div style="text-align:center;">}g;
			s{\[r\]}{<div style="text-align:right;">}g;
			s{\[j\]}{<div style="text-align:justify;">}g;
			s{\[/(l|c|r|j)\]}{</div>}g;

			s{\[ltr\]}{<div dir="ltr">}g;
			s{\[rtr\]}{<div dir="rtl">}g;
			s{\[/(rtl|ltr)\]}{</div>}g;

			s{\[nl\]}{<br />}g;
			s{\[hr\]}{<hr />}g;

			# listes
			while (m{\[liste "?(.)"?\][\r\n]*([^\[\]]+?)[\r\n]*\[/liste\]}s) {
 				my ($all, $type, $data) = ($&, $1, $2);
				# $data =~ s/^\n+//;	chomp $data;
				my $html = "";
				foreach my $l (split /[\r\n]+/, $data) {
					$html .= "<li>$l</li>";
				}
				if ($type =~ /^[1ai]$/i) {
					$html = "<ol type=\"$type\">$html</ol>";
				} else {
					$html = "<ul>$html</ul>";
				}
				s{\Q$all\E[\r\n]*}{$html}s;
#				warn "DATA\n$data\nHTML\n$html\n\n";
			}

			s{\r?\n}{<br />\n}g;
		}

		push @res, $_;
#		warn "$_\n";
	}
	push @res, @{$row}[0..$idnum];
	return @res;
}


#################################################################


__END__

=head1 NAME

esprit_convHTML.pl

=head1 SYNOPSIS

esprit_convHTML.pl [options] database

Convertit la base de données d'Esprit du format meta [...] au HTML <...>.

Il est conseillé de faire un dump ("mysqldump -e ...") avant d'appliquer ce script.

=head1 OPTIONS

=over 8

=item B<-h, --help>

Affiche une page d'aide abrégée.

=item B<--man>

Affiche cette page de man.

=item B<--password, -p>

Demander le mot de passe.

=item B<--user, -u> user

L'utilisateur MySQL. Par défaut, esprit-admin.

=item B<-v, --verbose>

Incrémente la verbosité.

=back

=cut
