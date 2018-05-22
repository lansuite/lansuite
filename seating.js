function DrawClearSeatingSymbol(id, XOffset, YOffset, link, tooltip)
{
    ClearArea(XOffset, YOffset, 14, 14, link);
    DrawSeatingSymbol(id, XOffset, YOffset, link, tooltip);
}

var CurrentDrawingSymbol = '';
function UpdateCurrentDrawingSymbol(id)
{
    CurrentDrawingSymbol = parseInt(id);
    DrawClearSeatingSymbol(parseInt(id), 60, 0, '', '');
}

function DrawSeatingSymbol(id, XOffset, YOffset, link, tooltip)
{
    switch (id) {
    // Empty (mode 1)
        case 0: CreateSmallRect(XOffset, YOffset, 12, 12, '#9d9d9d', link, tooltip); break;
        // Seat free
        case 1: CreateSmallRect(XOffset, YOffset, 12, 12, '#77ce4e', link, tooltip); break;
    // Normal occupied seat
        case 2: CreateSmallRect(XOffset, YOffset, 12, 12, '#c83232', link, tooltip); break;
        // Seat marked
        case 3: CreateSmallRect(XOffset, YOffset, 12, 12, '#aaaa32', link, tooltip); break;
    // My Seat
        case 4: CreateSmallRect(XOffset, YOffset, 12, 12, '#3232aa', link, tooltip); break;
    // Clanmate
        case 5: CreateSmallRect(XOffset, YOffset, 12, 12, '#326eaa', link, tooltip); break;
    // Checked out
        case 6: CreateSmallRect(XOffset, YOffset, 12, 12, '#326e32', link, tooltip); break;
        // Seat reserved
        case 7: CreateSmallRect(XOffset, YOffset, 12, 12, '#9d9d9d', link, tooltip); break;
    // Checked in
        case 8: CreateSmallRect(XOffset, YOffset, 12, 12, '#6e3232', link, tooltip); break;
    // Straight lines
        case 10: CreateThickLinkLine(XOffset + 7, YOffset, XOffset + 7, YOffset + 14, '#000000', link); break;
        case 11: CreateThickLinkLine(XOffset, YOffset + 7, XOffset + 14, YOffset + 7, '#000000', link); break;
    // T-Lines
        case 12: CreateThickLinkLine(XOffset, YOffset + 7, XOffset + 14, YOffset + 7, '#000000', link);
            CreateThickLinkLine(XOffset + 7, YOffset, XOffset + 7, YOffset + 7, '#000000', link); break;
        case 13: CreateThickLinkLine(XOffset + 7, YOffset, XOffset + 7, YOffset + 14, '#000000', link);
            CreateThickLinkLine(XOffset, YOffset + 7, XOffset + 7, YOffset + 7, '#000000', link); break;
        case 14: CreateThickLinkLine(XOffset, YOffset + 7, XOffset + 14, YOffset + 7, '#000000', link);
            CreateThickLinkLine(XOffset + 7, YOffset + 7, XOffset + 7, YOffset + 14, '#000000', link); break;
        case 15: CreateThickLinkLine(XOffset + 7, YOffset, XOffset + 7, YOffset + 14, '#000000', link);
            CreateThickLinkLine(XOffset + 7, YOffset + 7, XOffset + 14, YOffset + 7, '#000000', link); break;
    // Cross
        case 16: CreateThickLinkLine(XOffset + 7, YOffset, XOffset + 7, YOffset + 14, '#000000', link);
            CreateThickLinkLine(XOffset, YOffset + 7, XOffset + 14, YOffset + 7, '#000000', link); break;
    // Corners
        case 17: CreateThickLinkLine(XOffset, YOffset + 7, XOffset + 9, YOffset + 7, '#000000', link);
            CreateThickLinkLine(XOffset + 7, YOffset, XOffset + 7, YOffset + 9, '#000000', link); break;
        case 18: CreateThickLinkLine(XOffset, YOffset + 7, XOffset + 9, YOffset + 7, '#000000', link);
            CreateThickLinkLine(XOffset + 7, YOffset + 9, XOffset + 7, YOffset + 14, '#000000', link); break;
        case 19: CreateThickLinkLine(XOffset + 5, YOffset + 7, XOffset + 14, YOffset + 7, '#000000', link);
            CreateThickLinkLine(XOffset + 7, YOffset + 5, XOffset + 7, YOffset + 14, '#000000', link); break;
        case 20: CreateThickLinkLine(XOffset + 5, YOffset + 7, XOffset + 14, YOffset + 7, '#000000', link);
            CreateThickLinkLine(XOffset + 7, YOffset, XOffset + 7, YOffset + 9, '#000000', link); break;
    // Diagonal
        case 21: CreateThickLinkLine(XOffset - 1, YOffset + 7, XOffset + 7, YOffset + 15, '#000000', link); break;
        case 22: CreateThickLinkLine(XOffset + 15, YOffset + 7, XOffset + 7, YOffset + 15, '#000000', link); break;
        case 23: CreateThickLinkLine(XOffset + 15, YOffset + 7, XOffset + 7, YOffset - 1, '#000000', link); break;
        case 24: CreateThickLinkLine(XOffset - 1, YOffset + 7, XOffset + 7, YOffset - 1, '#000000', link); break;
    // Straight lines
        case 101: CreateLinkLine(XOffset, YOffset + 7, XOffset + 14, YOffset + 7, '#000000', link); break;
        case 102: CreateLinkLine(XOffset + 7, YOffset, XOffset + 7, YOffset + 14, '#000000', link); break;
    // Corners
        case 103: CreateLinkLine(XOffset + 6, YOffset + 7, XOffset + 14, YOffset + 7, '#000000', link);
            CreateLinkLine(XOffset + 7, YOffset + 6, XOffset + 7, YOffset + 14, '#000000', link); break;
        case 104: CreateLinkLine(XOffset, YOffset + 7, XOffset + 8, YOffset + 7, '#000000', link);
            CreateLinkLine(XOffset + 7, YOffset + 8, XOffset + 7, YOffset + 14, '#000000', link); break;
        case 105: CreateLinkLine(XOffset + 6, YOffset + 7, XOffset + 14, YOffset + 7, '#000000', link);
            CreateLinkLine(XOffset + 7, YOffset, XOffset + 7, YOffset + 8, '#000000', link); break;
        case 106: CreateLinkLine(XOffset, YOffset + 7, XOffset + 8, YOffset + 7, '#000000', link);
            CreateLinkLine(XOffset + 7, YOffset, XOffset + 7, YOffset + 8, '#000000', link); break;
    // T-Lines
        case 107: CreateLinkLine(XOffset, YOffset + 7, XOffset + 14, YOffset + 7, '#000000', link);
            CreateLinkLine(XOffset + 7, YOffset + 7, XOffset + 7, YOffset + 14, '#000000', link); break;
        case 108: CreateLinkLine(XOffset + 7, YOffset, XOffset + 7, YOffset + 14, '#000000', link);
            CreateLinkLine(XOffset + 7, YOffset + 7, XOffset + 14, YOffset + 7, '#000000', link); break;
        case 109: CreateLinkLine(XOffset + 7, YOffset, XOffset + 7, YOffset + 14, '#000000', link);
            CreateLinkLine(XOffset, YOffset + 7, XOffset + 7, YOffset + 7, '#000000', link); break;
        case 110: CreateLinkLine(XOffset, YOffset + 7, XOffset + 14, YOffset + 7, '#000000', link);
            CreateLinkLine(XOffset + 7, YOffset, XOffset + 7, YOffset + 7, '#000000', link); break;
    // Cross
        case 111: CreateLinkLine(XOffset + 7, YOffset, XOffset + 7, YOffset + 14, '#000000', link);
            CreateLinkLine(XOffset, YOffset + 7, XOffset + 14, YOffset + 7, '#000000', link); break;
    // Diagonal. TODO: Should be round, and non-straight lines
        case 112: case 119: case 124: case 126: case 127: CreateLinkLine(XOffset + 14, YOffset + 7, XOffset + 7, YOffset + 14, '#000000', link); break;
        case 113: case 118: case 123: case 130: case 131: CreateLinkLine(XOffset, YOffset + 7, XOffset + 7, YOffset + 14, '#000000', link); break;
        case 114: case 117: case 120: case 122: case 125: CreateLinkLine(XOffset + 14, YOffset + 7, XOffset + 7, YOffset, '#000000', link); break;
        case 115: case 116: case 121: case 128: case 129: CreateLinkLine(XOffset, YOffset + 7, XOffset + 7, YOffset, '#000000', link); break;
    // Windows
    // Horizontal
        case 132: CreateLinkLine(XOffset + 7, YOffset + 14, XOffset + 5, YOffset + 11, '#000000', link);
            CreateLinkLine(XOffset + 7, YOffset + 14, XOffset + 9, YOffset + 11, '#000000', link);
            CreateLinkLine(XOffset + 5, YOffset + 3, XOffset + 5, YOffset + 11, '#000000', link);
            CreateLinkLine(XOffset + 9, YOffset + 3, XOffset + 9, YOffset + 11, '#000000', link);
            CreateLinkLine(XOffset + 7, YOffset, XOffset + 5, YOffset + 3, '#000000', link);
            CreateLinkLine(XOffset + 7, YOffset, XOffset + 9, YOffset + 3, '#000000', link); break;
        case 133: CreateLinkLine(XOffset + 5, YOffset + 14, XOffset + 5, YOffset + 8, '#000000', link);
            CreateLinkLine(XOffset + 9, YOffset + 14, XOffset + 9, YOffset + 8, '#000000', link);
            CreateLinkLine(XOffset + 5, YOffset + 8, XOffset + 7, YOffset, '#000000', link);
            CreateLinkLine(XOffset + 9, YOffset + 8, XOffset + 7, YOffset, '#000000', link); break;
        case 134: CreateLinkLine(XOffset + 5, YOffset, XOffset + 5, YOffset + 14, '#000000', link);
            CreateLinkLine(XOffset + 9, YOffset, XOffset + 9, YOffset + 14, '#000000', link); break;
        case 135: CreateLinkLine(XOffset + 5, YOffset, XOffset + 5, YOffset + 8, '#000000', link);
            CreateLinkLine(XOffset + 9, YOffset, XOffset + 9, YOffset + 8, '#000000', link);
            CreateLinkLine(XOffset + 5, YOffset + 8, XOffset + 7, YOffset + 14, '#000000', link);
            CreateLinkLine(XOffset + 9, YOffset + 8, XOffset + 7, YOffset + 14, '#000000', link); break;
    // Corners
        case 136: CreateLinkLine(XOffset + 14, YOffset + 7, XOffset + 7, YOffset + 7, '#000000', link);
            CreateLinkLine(XOffset + 7, YOffset + 7, XOffset + 5, YOffset + 11, '#000000', link);
            CreateLinkLine(XOffset + 7, YOffset + 7, XOffset + 9, YOffset + 11, '#000000', link);
            CreateLinkLine(XOffset + 5, YOffset + 11, XOffset + 5, YOffset + 14, '#000000', link);
            CreateLinkLine(XOffset + 9, YOffset + 11, XOffset + 9, YOffset + 14, '#000000', link); break;
        case 137: CreateLinkLine(XOffset, YOffset + 7, XOffset + 7, YOffset + 7, '#000000', link);
            CreateLinkLine(XOffset + 7, YOffset + 7, XOffset + 5, YOffset + 11, '#000000', link);
            CreateLinkLine(XOffset + 7, YOffset + 7, XOffset + 9, YOffset + 11, '#000000', link);
            CreateLinkLine(XOffset + 5, YOffset + 11, XOffset + 5, YOffset + 14, '#000000', link);
            CreateLinkLine(XOffset + 9, YOffset + 11, XOffset + 9, YOffset + 14, '#000000', link); break;
        case 138: CreateLinkLine(XOffset + 14, YOffset + 7, XOffset + 7, YOffset + 7, '#000000', link);
            CreateLinkLine(XOffset + 7, YOffset + 7, XOffset + 5, YOffset + 3, '#000000', link);
            CreateLinkLine(XOffset + 7, YOffset + 7, XOffset + 9, YOffset + 3, '#000000', link);
            CreateLinkLine(XOffset + 5, YOffset + 3, XOffset + 5, YOffset, '#000000', link);
            CreateLinkLine(XOffset + 9, YOffset + 3, XOffset + 9, YOffset, '#000000', link); break;
        case 139: CreateLinkLine(XOffset, YOffset + 7, XOffset + 7, YOffset + 7, '#000000', link);
            CreateLinkLine(XOffset + 7, YOffset + 7, XOffset + 5, YOffset + 3, '#000000', link);
            CreateLinkLine(XOffset + 7, YOffset + 7, XOffset + 9, YOffset + 3, '#000000', link);
            CreateLinkLine(XOffset + 5, YOffset + 3, XOffset + 5, YOffset, '#000000', link);
            CreateLinkLine(XOffset + 9, YOffset + 3, XOffset + 9, YOffset, '#000000', link); break;
        case 140: CreateLinkLine(XOffset + 7, YOffset + 14, XOffset + 7, YOffset + 7, '#000000', link);
            CreateLinkLine(XOffset + 7, YOffset + 7, XOffset + 11, YOffset + 5, '#000000', link);
            CreateLinkLine(XOffset + 7, YOffset + 7, XOffset + 11, YOffset + 9, '#000000', link);
            CreateLinkLine(XOffset + 11, YOffset + 5, XOffset + 14, YOffset + 5, '#000000', link);
            CreateLinkLine(XOffset + 11, YOffset + 9, XOffset + 14, YOffset + 9, '#000000', link); break;
        case 141: CreateLinkLine(XOffset + 7, YOffset + 14, XOffset + 7, YOffset + 7, '#000000', link);
            CreateLinkLine(XOffset + 7, YOffset + 7, XOffset + 3, YOffset + 5, '#000000', link);
            CreateLinkLine(XOffset + 7, YOffset + 7, XOffset + 3, YOffset + 9, '#000000', link);
            CreateLinkLine(XOffset + 3, YOffset + 5, XOffset, YOffset + 5, '#000000', link);
            CreateLinkLine(XOffset + 3, YOffset + 9, XOffset, YOffset + 9, '#000000', link); break;
        case 142: CreateLinkLine(XOffset + 7, YOffset, XOffset + 7, YOffset + 7, '#000000', link);
            CreateLinkLine(XOffset + 7, YOffset + 7, XOffset + 11, YOffset + 5, '#000000', link);
            CreateLinkLine(XOffset + 7, YOffset + 7, XOffset + 11, YOffset + 9, '#000000', link);
            CreateLinkLine(XOffset + 11, YOffset + 5, XOffset + 14, YOffset + 5, '#000000', link);
            CreateLinkLine(XOffset + 11, YOffset + 9, XOffset + 14, YOffset + 9, '#000000', link); break;
        case 143: CreateLinkLine(XOffset + 7, YOffset, XOffset + 7, YOffset + 7, '#000000', link);
            CreateLinkLine(XOffset + 7, YOffset + 7, XOffset + 3, YOffset + 5, '#000000', link);
            CreateLinkLine(XOffset + 7, YOffset + 7, XOffset + 3, YOffset + 9, '#000000', link);
            CreateLinkLine(XOffset + 3, YOffset + 5, XOffset, YOffset + 5, '#000000', link);
            CreateLinkLine(XOffset + 3, YOffset + 9, XOffset, YOffset + 9, '#000000', link); break;
    // Double-Window-Corners
        case 144: CreateLinkLine(XOffset + 7, YOffset + 7, XOffset + 11, YOffset + 5, '#000000', link);
            CreateLinkLine(XOffset + 7, YOffset + 7, XOffset + 11, YOffset + 9, '#000000', link);
            CreateLinkLine(XOffset + 11, YOffset + 5, XOffset + 14, YOffset + 5, '#000000', link);
            CreateLinkLine(XOffset + 11, YOffset + 9, XOffset + 14, YOffset + 9, '#000000', link);
            CreateLinkLine(XOffset + 7, YOffset + 7, XOffset + 5, YOffset + 11, '#000000', link);
            CreateLinkLine(XOffset + 7, YOffset + 7, XOffset + 9, YOffset + 11, '#000000', link);
            CreateLinkLine(XOffset + 5, YOffset + 11, XOffset + 5, YOffset + 14, '#000000', link);
            CreateLinkLine(XOffset + 9, YOffset + 11, XOffset + 9, YOffset + 14, '#000000', link); break;
        case 145: CreateLinkLine(XOffset + 7, YOffset + 7, XOffset + 3, YOffset + 5, '#000000', link);
            CreateLinkLine(XOffset + 7, YOffset + 7, XOffset + 3, YOffset + 9, '#000000', link);
            CreateLinkLine(XOffset + 3, YOffset + 5, XOffset, YOffset + 5, '#000000', link);
            CreateLinkLine(XOffset + 3, YOffset + 9, XOffset, YOffset + 9, '#000000', link);
            CreateLinkLine(XOffset + 7, YOffset + 7, XOffset + 5, YOffset + 11, '#000000', link);
            CreateLinkLine(XOffset + 7, YOffset + 7, XOffset + 9, YOffset + 11, '#000000', link);
            CreateLinkLine(XOffset + 5, YOffset + 11, XOffset + 5, YOffset + 14, '#000000', link);
            CreateLinkLine(XOffset + 9, YOffset + 11, XOffset + 9, YOffset + 14, '#000000', link); break;
        case 146: CreateLinkLine(XOffset + 7, YOffset + 7, XOffset + 11, YOffset + 5, '#000000', link);
            CreateLinkLine(XOffset + 7, YOffset + 7, XOffset + 11, YOffset + 9, '#000000', link);
            CreateLinkLine(XOffset + 11, YOffset + 5, XOffset + 14, YOffset + 5, '#000000', link);
            CreateLinkLine(XOffset + 11, YOffset + 9, XOffset + 14, YOffset + 9, '#000000', link);
            CreateLinkLine(XOffset + 7, YOffset + 7, XOffset + 5, YOffset + 3, '#000000', link);
            CreateLinkLine(XOffset + 7, YOffset + 7, XOffset + 9, YOffset + 3, '#000000', link);
            CreateLinkLine(XOffset + 5, YOffset + 3, XOffset + 5, YOffset, '#000000', link);
            CreateLinkLine(XOffset + 9, YOffset + 3, XOffset + 9, YOffset, '#000000', link); break;
        case 147: CreateLinkLine(XOffset + 7, YOffset + 7, XOffset + 3, YOffset + 5, '#000000', link);
            CreateLinkLine(XOffset + 7, YOffset + 7, XOffset + 3, YOffset + 9, '#000000', link);
            CreateLinkLine(XOffset + 3, YOffset + 5, XOffset, YOffset + 5, '#000000', link);
            CreateLinkLine(XOffset + 3, YOffset + 9, XOffset, YOffset + 9, '#000000', link);
            CreateLinkLine(XOffset + 7, YOffset + 7, XOffset + 5, YOffset + 3, '#000000', link);
            CreateLinkLine(XOffset + 7, YOffset + 7, XOffset + 9, YOffset + 3, '#000000', link);
            CreateLinkLine(XOffset + 5, YOffset + 3, XOffset + 5, YOffset, '#000000', link);
            CreateLinkLine(XOffset + 9, YOffset + 3, XOffset + 9, YOffset, '#000000', link); break;
    // Window-Corners
        case 148: CreateLinkLine(XOffset + 5, YOffset + 14, XOffset + 5, YOffset + 5, '#000000', link);
            CreateLinkLine(XOffset + 9, YOffset + 14, XOffset + 9, YOffset + 9, '#000000', link);
            CreateLinkLine(XOffset + 5, YOffset + 5, XOffset + 14, YOffset + 5, '#000000', link);
            CreateLinkLine(XOffset + 9, YOffset + 9, XOffset + 14, YOffset + 9, '#000000', link); break;
        case 149: CreateLinkLine(XOffset, YOffset + 5, XOffset + 9, YOffset + 5, '#000000', link);
            CreateLinkLine(XOffset, YOffset + 9, XOffset + 5, YOffset + 9, '#000000', link);
            CreateLinkLine(XOffset + 9, YOffset + 5, XOffset + 9, YOffset + 14, '#000000', link);
            CreateLinkLine(XOffset + 5, YOffset + 9, XOffset + 5, YOffset + 14, '#000000', link); break;
        case 150: CreateLinkLine(XOffset + 14, YOffset + 5, XOffset + 9, YOffset + 5, '#000000', link);
            CreateLinkLine(XOffset + 14, YOffset + 9, XOffset + 5, YOffset + 9, '#000000', link);
            CreateLinkLine(XOffset + 9, YOffset + 5, XOffset + 9, YOffset, '#000000', link);
            CreateLinkLine(XOffset + 5, YOffset + 9, XOffset + 5, YOffset, '#000000', link); break;
        case 151: CreateLinkLine(XOffset + 5, YOffset, XOffset + 5, YOffset + 5, '#000000', link);
            CreateLinkLine(XOffset + 9, YOffset, XOffset + 9, YOffset + 9, '#000000', link);
            CreateLinkLine(XOffset + 5, YOffset + 5, XOffset, YOffset + 5, '#000000', link);
            CreateLinkLine(XOffset + 9, YOffset + 9, XOffset, YOffset + 9, '#000000', link); break;
    // Vertical
        case 152: CreateLinkLine(XOffset + 14, YOffset + 7, XOffset + 11, YOffset + 5, '#000000', link);
            CreateLinkLine(XOffset + 14, YOffset + 7, XOffset + 11, YOffset + 9, '#000000', link);
            CreateLinkLine(XOffset + 11, YOffset + 5, XOffset + 3, YOffset + 5, '#000000', link);
            CreateLinkLine(XOffset + 11, YOffset + 9, XOffset + 3, YOffset + 9, '#000000', link);
            CreateLinkLine(XOffset + 3, YOffset + 5, XOffset, YOffset + 7, '#000000', link);
            CreateLinkLine(XOffset + 3, YOffset + 9, XOffset, YOffset + 7, '#000000', link); break;
        case 153: CreateLinkLine(XOffset + 14, YOffset + 5, XOffset + 8, YOffset + 5, '#000000', link);
            CreateLinkLine(XOffset + 14, YOffset + 9, XOffset + 8, YOffset + 9, '#000000', link);
            CreateLinkLine(XOffset + 8, YOffset + 5, XOffset, YOffset + 7, '#000000', link);
            CreateLinkLine(XOffset + 8, YOffset + 9, XOffset, YOffset + 7, '#000000', link); break;
        case 154: CreateLinkLine(XOffset, YOffset + 5, XOffset + 14, YOffset + 5, '#000000', link);
            CreateLinkLine(XOffset, YOffset + 9, XOffset + 14, YOffset + 9, '#000000', link); break;
        case 156: CreateLinkLine(XOffset, YOffset + 5, XOffset + 8, YOffset + 5, '#000000', link);
            CreateLinkLine(XOffset, YOffset + 9, XOffset + 8, YOffset + 9, '#000000', link);
            CreateLinkLine(XOffset + 8, YOffset + 5, XOffset + 14, YOffset + 7, '#000000', link);
            CreateLinkLine(XOffset + 8, YOffset + 9, XOffset + 14, YOffset + 7, '#000000', link); break;
    // Blue Boxes
    // Blue point
        case 201: case 202: case 203: case 204: case 205: CreateThickLinkLine(XOffset + 5, YOffset + 7, XOffset + 9, YOffset + 7, '#0c00ff', link); break;
    // Blue lines
        case 207: CreateLinkLine(XOffset + 3, YOffset, XOffset + 3, YOffset + 14, '#0c00ff', link);
            CreateThickLinkLine(XOffset + 6, YOffset, XOffset + 6, YOffset + 14, '#8d89f1', link); break;
        case 216: CreateLinkLine(XOffset + 11, YOffset, XOffset + 11, YOffset + 14, '#0c00ff', link);
            CreateThickLinkLine(XOffset + 8, YOffset, XOffset + 8, YOffset + 14, '#8d89f1', link); break;
        case 212: CreateLinkLine(XOffset, YOffset + 3, XOffset + 14, YOffset + 3, '#0c00ff', link);
            CreateThickLinkLine(XOffset, YOffset + 6, XOffset + 14, YOffset + 6, '#8d89f1', link); break;
        case 220: CreateLinkLine(XOffset, YOffset + 11, XOffset + 14, YOffset + 11, '#0c00ff', link);
            CreateThickLinkLine(XOffset, YOffset + 8, XOffset + 14, YOffset + 8, '#8d89f1', link); break;
    // Blue corners
        case 206: case 211: case 214:
                    CreateLinkLine(XOffset + 3, YOffset + 14, XOffset + 3, YOffset + 3, '#0c00ff', link);
                    CreateLinkLine(XOffset + 3, YOffset + 3, XOffset + 14, YOffset + 3, '#0c00ff', link);
                    CreateThickLinkLine(XOffset + 6, YOffset + 14, XOffset + 6, YOffset + 6, '#8d89f1', link);
                    CreateThickLinkLine(XOffset + 4, YOffset + 6, XOffset + 14, YOffset + 6, '#8d89f1', link); break;
        case 209: case 217: case 219:
                    CreateLinkLine(XOffset + 11, YOffset, XOffset + 11, YOffset + 11, '#0c00ff', link);
                    CreateLinkLine(XOffset + 11, YOffset + 11, XOffset, YOffset + 11, '#0c00ff', link);
                    CreateThickLinkLine(XOffset + 8, YOffset, XOffset + 8, YOffset + 8, '#8d89f1', link);
                    CreateThickLinkLine(XOffset + 10, YOffset + 8, XOffset, YOffset + 8, '#8d89f1', link); break;
        case 208: case 210: case 221:
                    CreateLinkLine(XOffset + 3, YOffset, XOffset + 3, YOffset + 11, '#0c00ff', link);
                    CreateLinkLine(XOffset + 3, YOffset + 11, XOffset + 14, YOffset + 11, '#0c00ff', link);
                    CreateThickLinkLine(XOffset + 6, YOffset, XOffset + 6, YOffset + 8, '#8d89f1', link);
                    CreateThickLinkLine(XOffset + 4, YOffset + 8, XOffset + 14, YOffset + 8, '#8d89f1', link); break;
        case 213: case 215: case 218:
                    CreateLinkLine(XOffset + 11, YOffset + 14, XOffset + 11, YOffset + 3, '#0c00ff', link);
                    CreateLinkLine(XOffset + 11, YOffset + 3, XOffset, YOffset + 3, '#0c00ff', link);
                    CreateThickLinkLine(XOffset + 8, YOffset + 14, XOffset + 8, YOffset + 6, '#8d89f1', link);
                    CreateThickLinkLine(XOffset + 10, YOffset + 6, XOffset, YOffset + 6, '#8d89f1', link); break;
    // Yellow Boxes
        case 222: CreateThickLinkLine(XOffset + 5, YOffset + 7, XOffset + 9, YOffset + 7, '#cac861', link); break;
        case 223: CreateThickLinkLine(XOffset + 5, YOffset + 7, XOffset + 14, YOffset + 7, '#cac861', link); break;
        case 224: CreateThickLinkLine(XOffset, YOffset + 7, XOffset + 14, YOffset + 7, '#cac861', link); break;
        case 225: CreateThickLinkLine(XOffset, YOffset + 7, XOffset + 9, YOffset + 7, '#cac861', link); break;
        case 226: CreateThickLinkLine(XOffset + 7, YOffset + 5, XOffset + 7, YOffset + 14, '#cac861', link); break;
        case 227: CreateThickLinkLine(XOffset + 7, YOffset, XOffset + 7, YOffset + 14, '#cac861', link); break;
        case 228: CreateThickLinkLine(XOffset + 7, YOffset, XOffset + 7, YOffset + 9, '#cac861', link); break;
    // Letters and signs
        case 300: CreateText('A', XOffset + 2, YOffset + 11, link); break;
        case 301: CreateText('B', XOffset + 2, YOffset + 11, link); break;
        case 302: CreateText('C', XOffset + 2, YOffset + 11, link); break;
        case 303: CreateText('D', XOffset + 2, YOffset + 11, link); break;
        case 304: CreateText('E', XOffset + 2, YOffset + 11, link); break;
        case 305: CreateText('F', XOffset + 2, YOffset + 11, link); break;
        case 306: CreateText('G', XOffset + 2, YOffset + 11, link); break;
        case 307: CreateText('H', XOffset + 2, YOffset + 11, link); break;
        case 308: CreateText('I', XOffset + 2, YOffset + 11, link); break;
        case 309: CreateText('J', XOffset + 2, YOffset + 11, link); break;
        case 310: CreateText('K', XOffset + 2, YOffset + 11, link); break;
        case 311: CreateText('L', XOffset + 2, YOffset + 11, link); break;
        case 312: CreateText('M', XOffset + 2, YOffset + 11, link); break;
        case 313: CreateText('N', XOffset + 2, YOffset + 11, link); break;
        case 314: CreateText('O', XOffset + 2, YOffset + 11, link); break;
        case 315: CreateText('P', XOffset + 2, YOffset + 11, link); break;
        case 316: CreateText('Q', XOffset + 2, YOffset + 11, link); break;
        case 317: CreateText('R', XOffset + 2, YOffset + 11, link); break;
        case 318: CreateText('S', XOffset + 2, YOffset + 11, link); break;
        case 319: CreateText('T', XOffset + 2, YOffset + 11, link); break;
        case 320: CreateText('U', XOffset + 2, YOffset + 11, link); break;
        case 321: CreateText('V', XOffset + 2, YOffset + 11, link); break;
        case 322: CreateText('W', XOffset + 2, YOffset + 11, link); break;
        case 323: CreateText('X', XOffset + 2, YOffset + 11, link); break;
        case 324: CreateText('Y', XOffset + 2, YOffset + 11, link); break;
        case 325: CreateText('Z', XOffset + 2, YOffset + 11, link); break;
        case 326: CreateText('Ä', XOffset + 2, YOffset + 11, link); break;
        case 327: CreateText('Ö', XOffset + 2, YOffset + 11, link); break;
        case 328: CreateText('Ü', XOffset + 2, YOffset + 11, link); break;
        case 329: CreateText('-', XOffset + 2, YOffset + 11, link); break;
        case 330: CreateText('+', XOffset + 2, YOffset + 11, link); break;
        case 331: CreateText('/', XOffset + 2, YOffset + 11, link); break;
        case 332: CreateText('&', XOffset + 2, YOffset + 11, link); break;
        case 333: CreateText('*', XOffset + 2, YOffset + 11, link); break;
        case 334: CreateText('<', XOffset + 2, YOffset + 11, link); break;
        case 335: CreateText('>', XOffset + 2, YOffset + 11, link); break;
        case 336: CreateText('@', XOffset + 2, YOffset + 11, link); break;
        case 337: CreateText('€', XOffset + 2, YOffset + 11, link); break;
        case 338: CreateText(',', XOffset + 2, YOffset + 11, link); break;
        case 339: CreateText('.', XOffset + 2, YOffset + 11, link); break;
        case 340: CreateText(';', XOffset + 2, YOffset + 11, link); break;
        case 341: CreateText('!', XOffset + 2, YOffset + 11, link); break;
        case 342: CreateText('?', XOffset + 2, YOffset + 11, link); break;
        case 343: CreateText('a', XOffset + 2, YOffset + 11, link); break;
        case 344: CreateText('b', XOffset + 2, YOffset + 11, link); break;
        case 345: CreateText('c', XOffset + 2, YOffset + 11, link); break;
        case 346: CreateText('d', XOffset + 2, YOffset + 11, link); break;
        case 347: CreateText('e', XOffset + 2, YOffset + 11, link); break;
        case 348: CreateText('f', XOffset + 2, YOffset + 11, link); break;
        case 349: CreateText('g', XOffset + 2, YOffset + 11, link); break;
        case 350: CreateText('h', XOffset + 2, YOffset + 11, link); break;
        case 351: CreateText('i', XOffset + 2, YOffset + 11, link); break;
        case 352: CreateText('j', XOffset + 2, YOffset + 11, link); break;
        case 353: CreateText('k', XOffset + 2, YOffset + 11, link); break;
        case 354: CreateText('l', XOffset + 2, YOffset + 11, link); break;
        case 355: CreateText('m', XOffset + 2, YOffset + 11, link); break;
        case 356: CreateText('n', XOffset + 2, YOffset + 11, link); break;
        case 357: CreateText('o', XOffset + 2, YOffset + 11, link); break;
        case 358: CreateText('p', XOffset + 2, YOffset + 11, link); break;
        case 359: CreateText('q', XOffset + 2, YOffset + 11, link); break;
        case 360: CreateText('r', XOffset + 2, YOffset + 11, link); break;
        case 361: CreateText('s', XOffset + 2, YOffset + 11, link); break;
        case 362: CreateText('t', XOffset + 2, YOffset + 11, link); break;
        case 363: CreateText('u', XOffset + 2, YOffset + 11, link); break;
        case 364: CreateText('v', XOffset + 2, YOffset + 11, link); break;
        case 365: CreateText('w', XOffset + 2, YOffset + 11, link); break;
        case 366: CreateText('x', XOffset + 2, YOffset + 11, link); break;
        case 367: CreateText('y', XOffset + 2, YOffset + 11, link); break;
        case 368: CreateText('z', XOffset + 2, YOffset + 11, link); break;
        case 369: CreateText('ä', XOffset + 2, YOffset + 11, link); break;
        case 370: CreateText('ö', XOffset + 2, YOffset + 11, link); break;
        case 371: CreateText('ü', XOffset + 2, YOffset + 11, link); break;
        case 372: CreateText('1', XOffset + 2, YOffset + 11, link); break;
        case 373: CreateText('2', XOffset + 2, YOffset + 11, link); break;
        case 374: CreateText('3', XOffset + 2, YOffset + 11, link); break;
        case 375: CreateText('4', XOffset + 2, YOffset + 11, link); break;
        case 376: CreateText('5', XOffset + 2, YOffset + 11, link); break;
        case 377: CreateText('6', XOffset + 2, YOffset + 11, link); break;
        case 378: CreateText('7', XOffset + 2, YOffset + 11, link); break;
        case 379: CreateText('8', XOffset + 2, YOffset + 11, link); break;
        case 380: CreateText('9', XOffset + 2, YOffset + 11, link); break;
        case 381: CreateText('0', XOffset + 2, YOffset + 11, link); break;
        case 382: CreateText('[', XOffset + 2, YOffset + 11, link); break;
        case 383: CreateText(']', XOffset + 2, YOffset + 11, link); break;
    }
}

// Functions to Change Image
function ChangeSeatingPlan(CellId, XOffset, YOffset)
{

  // Switch image
    ClearArea(XOffset, YOffset, 14, 14, 'javascript:ChangeSeatingPlan(\"'+ CellId +'\", '+ XOffset +', '+ YOffset +')');
    DrawSeatingSymbol(parseInt(CurrentDrawingSymbol), XOffset, YOffset, 'javascript:ChangeSeatingPlan(\"'+ CellId +'\", '+ XOffset +', '+ YOffset +')', 't');
  
  // Update hidden field, for DB
    document.getElementById(CellId).value = CurrentDrawingSymbol;
}