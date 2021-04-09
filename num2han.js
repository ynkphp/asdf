function num2han(num) {
    var hans = ['', '일', '이', '삼', '사', '오', '육', '칠', '팔', '구'],
        units1 = ['', '', '십', '백', '천'],
        units4 = ['', '만', '억', '조', '경', '해', '자', '양', '구(溝)', '간', '정', '재', '극', '항하사', '아승기', '나유타', '불가사의', '무량대수', '겁'],
        rt = '', chunk = '',
        han, unit1, unit4, i, pos, pos1, pos4;

    num = ('' + num).replace(/[^0-9]/g, '');
    pos = num.length;
    pos4 = Math.ceil(pos / 4);
    if (pos4 <= units4.length) {
        for (var i in num) {
            han = hans[num[i]];
            pos = num.length - i;
            pos1 = pos % 4 || 4;
            unit1 = units1[pos1];
            if (han != '') {
                if (han == '일' && i > 0) {
                    chunk += unit1 || '일';
                } else {
                    chunk += han + unit1;
                }
            }
            if (pos1 == 1 && chunk != '') {
                pos4 = Math.floor(pos / 4);
                unit4 = units4[pos4];
                rt += chunk + unit4;
                chunk = '';
            }
        }
    } else {
        rt = num;
    }

    return rt;
}
