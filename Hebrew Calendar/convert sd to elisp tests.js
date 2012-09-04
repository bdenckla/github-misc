process.stdin.resume();
process.stdin.setEncoding('utf8');

var data = '';

process.stdin.on('data', function (chunk) {
    data += chunk;
});

process.stdin.on('end', function () {
    parsed = JSON.parse( data );

    pd = parsed.data;

    pd.forEach( handle_date );
});

function handle_date( date )
{
    rd = date[0];
    hy = date[1];
    hm = date[2];
    hd = date[3];

    var outstr =
        'rd: ' + rd + ' '
        +
        'hy: ' + hy + ' '
        +
        'hm: ' + hm + ' '
        +
        'hd: ' + hd + '\n';
    ;

    process.stdout.write( outstr );
}