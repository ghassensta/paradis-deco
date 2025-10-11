/**
 * App eCommerce Category List
 */

'use strict';

// Comment editor

const commentEditor = document.querySelector('.comment-editor'),
  commentEditor2 = document.querySelector('.comment-editor2');

if (commentEditor) {
  new Quill(commentEditor, {
    modules: {
      toolbar: '.comment-toolbar'
    },
    placeholder: 'Saisir la description de la catégorie...',
    theme: 'snow'
  });
}
if (commentEditor2) {
  new Quill(commentEditor2, {
    modules: {
      toolbar: '.comment-toolbar2'
    },
    placeholder: 'Saisir la description de la catégorie...',
    theme: 'snow'
  });
}

// Datatable (jquery)

$(function () {
  let borderColor, bodyBg, headingColor;

  if (isDarkStyle) {
    borderColor = config.colors_dark.borderColor;
    bodyBg = config.colors_dark.bodyBg;
    headingColor = config.colors_dark.headingColor;
  } else {
    borderColor = config.colors.borderColor;
    bodyBg = config.colors.bodyBg;
    headingColor = config.colors.headingColor;
  }

  // Variable declaration for category list table
  var dt_category_list_table = $('.datatables-category-list');

  //select2 for dropdowns in offcanvas

  var select2 = $('.select2');
  if (select2.length) {
    select2.each(function () {
      var $this = $(this);
      $this.wrap('<div class="position-relative"></div>').select2({
        dropdownParent: $this.parent(),
        placeholder: $this.data('placeholder') //for dynamic placeholder
      });
    });
  }

  // Customers List Datatable
 var addCategory = 'add.html';
  if (dt_category_list_table.length) {
    var dt_category = dt_category_list_table.DataTable({
      ajax: assetsPath + 'json/ecommerce-category-list.json', // JSON file to add data
      columns: [
        // columns according to JSON
        { data: '' },
        { data: 'id' },
        { data: 'categories' },
        { data: 'category_parent'},
        { data: 'category_section'},
        { data: 'total_products' },
        { data: '' }
      ],
      columnDefs: [
        {
          // For Responsive
          className: 'control',
          searchable: false,
          orderable: false,
          responsivePriority: 1,
          targets: 0,
          render: function (data, type, full, meta) {
            return '';
          }
        },
        {
          // For Checkboxes
          targets: 1,
          orderable: false,
          searchable: false,
          responsivePriority: 4,
          checkboxes: true,
          render: function () {
            return '<input type="checkbox" class="dt-checkboxes form-check-input">';
          },
          checkboxes: {
            selectAllRender: '<input type="checkbox" class="form-check-input">'
          }
        },
        {
          // Categories and Category Detail
          targets: 2,
          responsivePriority: 2,
          render: function (data, type, full, meta) {
            var $name = full['categories'];
              
            return (
            '<span class="text-body text-wrap fw-medium">' +
              $name +
              '</span>' )
          }
        },
        {
          targets:3,
          responsivePriority:4,
          orderable: true,
          searchable: false,
          render: function(data, type, full, meta) {
            var $desc = full['category_parent'];
            return '<p class="text-muted mb-0 d-none d-sm-block">' + $desc + '</p>';
          }
        },
        {
          targets:4,
          responsivePriority:5,
          orderable: true,
          searchable: false,
          render: function(data, type, full, meta) {
            var $desc = full['category_section'];
            return '<p class="text-muted mb-0 d-none d-sm-block">' + $desc + '</p>';
          }
        },
        {
          // Total products
          targets: 5,
          responsivePriority: 6,
          render: function (data, type, full, meta) {
            var $total_products = full['total_products'];
            return '<div class="text-sm-end">' + $total_products + '</div>';
          }
        },
       
        {
          // Actions
          targets: -1,
          title: 'Actions',
          searchable: false,
          orderable: false,
          render: function (data, type, full, meta) {
            return (
              '<div class="d-flex align-items-sm-center justify-content-sm-center">' +
              '<button class="btn btn-sm btn-icon delete-record me-2"><i class="ti ti-trash"></i></button>' +
              '<a href="edit.html?id='+full["id"]+'" class="btn btn-sm btn-icon" ><i class="ti ti-edit"></i></a>' +
              '</div>'
            );
          }
        }
      ],
      order: [2, 'desc'], //set any columns order asc/desc
      dom:
        '<"card-header d-flex flex-wrap pb-2"' +
        '<f>' +
        '<"d-flex justify-content-center justify-content-md-end align-items-baseline"<"dt-action-buttons d-flex justify-content-center flex-md-row mb-3 mb-md-0 ps-1 ms-1 align-items-baseline"lB>>' +
        '>t' +
        '<"row mx-2"' +
        '<"col-sm-12 col-md-6"i>' +
        '<"col-sm-12 col-md-6"p>' +
        '>',
      lengthMenu: [7, 10, 20, 50, 70, 100], //for length of menu
      language: {
        sEmptyTable:     "Aucune donnée disponible dans le tableau",
        sInfo:           "Affichage de l'élément _START_ à _END_ sur _TOTAL_ éléments",
        sInfoEmpty:      "Affichage de l'élément 0 à 0 sur 0 élément",
        sInfoFiltered:   "(filtré à partir de _MAX_ éléments au total)",
        sInfoPostFix:    "",
        sInfoThousands:  ",",
        sLengthMenu:     "Afficher _MENU_ éléments",
        sLoadingRecords: "Chargement...",
        sProcessing:     "Traitement...",
        sSearch:         "",
        sZeroRecords:    "Aucun résultat trouvé",
        oPaginate: {
            sFirst:    "Premier",
            sLast:     "Dernier",
            sNext:     "Suivant",
            sPrevious: "Précédent"
        },
        oAria: {
            sSortAscending:  ": activer pour trier la colonne par ordre croissant",
            sSortDescending: ": activer pour trier la colonne par ordre décroissant"
        },
        searchPlaceholder: "Chercher.."
    },
      // Button for offcanvas
      buttons: [
        {
          text: '<i class="ti ti-plus me-0 me-sm-1 ti-xs"></i><span class="d-none d-sm-inline-block">Ajouter Catégorie</span>',
          className: 'add-new btn btn-primary ms-2 ms-sm-0',
          action: function () {
            window.location.href = addCategory;
          }
        }
      ],
      // For responsive popup
      responsive: {
        details: {
          display: $.fn.dataTable.Responsive.display.modal({
            header: function (row) {
              var data = row.data();
              return 'Details of ' + data['categories'];
            }
          }),
          type: 'column',
          renderer: function (api, rowIdx, columns) {
            var data = $.map(columns, function (col, i) {
              return col.title !== '' // ? Do not show row in modal popup if title is blank (for check box)
                ? '<tr data-dt-row="' +
                    col.rowIndex +
                    '" data-dt-column="' +
                    col.columnIndex +
                    '">' +
                    '<td> ' +
                    col.title +
                    ':' +
                    '</td> ' +
                    '<td class="ps-0">' +
                    col.data +
                    '</td>' +
                    '</tr>'
                : '';
            }).join('');

            return data ? $('<table class="table"/><tbody />').append(data) : false;
          }
        }
      }
    });
    $('.dt-action-buttons').addClass('pt-0');
    $('.dataTables_filter').addClass('me-3 ps-0');
  }

  // Delete Record
  $('.datatables-category-list tbody').on('click', '.delete-record', function () {
    var row = $(this).closest('tr');
    Swal.fire({
      title: 'Êtes-vous sûr(e) ?',
      text: "Vous ne pourrez pas revenir en arrière !",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Oui, supprimez-le !',
      cancelButtonText: 'Non, annulez !',
      customClass: {
        confirmButton: 'btn btn-primary me-3',
        cancelButton: 'btn btn-label-secondary'
      },
      buttonsStyling: false
    }).then((result) => {
      if (result.isConfirmed) {
        dt_category.row(row).remove().draw();
        Swal.fire({
          icon: 'success',
          title: 'Supprimé !',
          text: 'Votre fichier a été supprimé.',
          customClass: {
            confirmButton: 'btn btn-success'
          }
        });
      } else if (result.dismiss === Swal.DismissReason.cancel) {
        Swal.fire({
          icon: 'error',
          title: 'Annulé',
          text: 'Votre fichier imaginaire est en sécurité :)',
          customClass: {
            confirmButton: 'btn btn-success'
          }
        });
      }
    });
  });

  // Filter form control to default size
  // ? setTimeout used for multilingual table initialization
  setTimeout(() => {
    $('.dataTables_filter .form-control').removeClass('form-control-sm');
    $('.dataTables_length .form-select').removeClass('form-select-sm');
  }, 300);
 
});

//For form validation
(function () {
  const eCommerceCategoryListForm = document.getElementById('eCommerceCategoryListForm');

  //Add New customer Form Validation
  const fv = FormValidation.formValidation(eCommerceCategoryListForm, {
    fields: {
      categoryTitle: {
        validators: {
          notEmpty: {
            message: 'Please enter category title'
          }
        }
      },
      slug: {
        validators: {
          notEmpty: {
            message: 'Please enter slug'
          }
        }
      }
    },
    plugins: {
      trigger: new FormValidation.plugins.Trigger(),
      bootstrap5: new FormValidation.plugins.Bootstrap5({
        // Use this for enabling/changing valid/invalid class
        eleValidClass: 'is-valid',
        rowSelector: function (field, ele) {
          // field is the field name & ele is the field element
          return '.mb-3';
        }
      }),
      submitButton: new FormValidation.plugins.SubmitButton(),
      // Submit the form when all fields are valid
      // defaultSubmit: new FormValidation.plugins.DefaultSubmit(),
      autoFocus: new FormValidation.plugins.AutoFocus()
    }
  });
})();
