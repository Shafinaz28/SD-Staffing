/**
 * ONE-TIME SETUP (2 minutes) — then Form → Sheet → website works automatically
 *
 * 1. Open your sheet:
 *    https://docs.google.com/spreadsheets/d/1ndJnRvRoTkJcbXCn0gzOOQNPH_XzpcVd6JuIceXqaMo/edit
 * 2. Extensions → Apps Script → paste this file → Save
 * 3. Deploy → New deployment → Web app
 *    Execute as: Me | Who has access: Anyone
 * 4. Copy the Web app URL into job.html → SHEETS_JOBS_API
 */

var SPREADSHEET_ID = '1ndJnRvRoTkJcbXCn0gzOOQNPH_XzpcVd6JuIceXqaMo';
var SHEET_GID = 274791102;

function doGet(e) {
  if (e && e.parameter && e.parameter.action === 'jobs') {
    return ContentService.createTextOutput(JSON.stringify(readJobsAsJson()))
      .setMimeType(ContentService.MimeType.JSON);
  }
  return ContentService.createTextOutput(JSON.stringify([]))
    .setMimeType(ContentService.MimeType.JSON);
}

function readJobsAsJson() {
  var ss = SpreadsheetApp.openById(SPREADSHEET_ID);
  var sheet = null;
  var sheets = ss.getSheets();
  for (var i = 0; i < sheets.length; i++) {
    if (sheets[i].getSheetId() === SHEET_GID) {
      sheet = sheets[i];
      break;
    }
  }
  if (!sheet) sheet = sheets[0];

  var values = sheet.getDataRange().getValues();
  if (values.length < 2) return [];

  var headers = values[0].map(function (h) { return String(h || '').trim(); });
  var col = {
    timestamp: colIndex_(headers, ['timestamp']),
    title: colIndex_(headers, ['title']),
    description: colIndex_(headers, ['description']),
    company: colIndex_(headers, ['company']),
    category: colIndex_(headers, ['category']),
    location: colIndex_(headers, ['location (select city)', 'location']),
    area: colIndex_(headers, ['area / locality', 'area']),
    salary: colIndex_(headers, ['salary']),
    experience: colIndex_(headers, ['experience'])
  };

  var jobs = [];
  for (var r = 1; r < values.length; r++) {
    var row = values[r];
    if (!String(row.join('')).trim()) continue;
    var title = val_(row, col.title);
    var company = val_(row, col.company);
    if (!title && !company) continue;
    jobs.push({
      id: String(r),
      timestamp: val_(row, col.timestamp),
      title: title,
      description: val_(row, col.description),
      company: company,
      category: val_(row, col.category) || 'General',
      location: val_(row, col.location),
      area: val_(row, col.area),
      salary: val_(row, col.salary),
      experience: val_(row, col.experience)
    });
  }

  jobs.sort(function (a, b) {
    return new Date(b.timestamp || 0) - new Date(a.timestamp || 0);
  });
  return jobs;
}

function colIndex_(headers, names) {
  for (var n = 0; n < names.length; n++) {
    var want = names[n].toLowerCase();
    for (var i = 0; i < headers.length; i++) {
      var h = headers[i].toLowerCase();
      if (h === want || h.indexOf(want) !== -1) return i;
    }
  }
  return -1;
}

function val_(row, i) {
  if (i < 0 || i >= row.length) return '';
  return String(row[i] == null ? '' : row[i]).trim();
}
