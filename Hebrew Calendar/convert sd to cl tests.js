
process.stdin.resume();
process.stdin.setEncoding('utf8');

var data = '';

process.stdin.on('data', function (chunk) {
    data += chunk;
});

process.stdin.on('end', function () {
    parsed = JSON.parse( data );

    pd = parsed.data;

    header =
        '(defun hebrew-calendar-conversion-test-data ()\n' +
        '(list\n';

    process.stdout.write( header );

    pd.forEach( handle_date );

    footer = '))';

    process.stdout.write( footer );
});

function handle_date( date )
{
    rd = date[0];
    hy = date[1];
    hm = date[2];
    hd = date[3];

    var outstr =
        '        ' +
        '(list ' +
        rd + ' (list '
        + hy + ' '
        + hm + ' '
        + hd + '))\n'
    ;

    process.stdout.write( outstr );
}