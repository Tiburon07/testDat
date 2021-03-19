/**
 * @author Geosystems srl - TI
 * ---------------------------------------------------------------
 * Oggetto gestione dell'interfaccia south panel tab Elaborazione
 *
 */
(function() {

    let _this = null;

    /**
     * Costruttore della classe ICElaborazioneManager
     * @constructor
     */
    UploadDATManager = function() {

        /*------------------------------------------------------------------
        --- VARIABILI DI CLASSE ELEMENTI HTML ---
        --------------------------------------------------------------------*/
        _this = this;
        this._form = $('#form_salva_gtfs');
        this._inputFile = $('#input_file_gtfs');
        this._btnSalva = $('#btn_salva_gtfs');
        this._btnScarica = $('#btn_scarica_gtfs');
        this._inpIdArchivio = $('#input_id_gtfs');
        this._customFileInput = $(".custom-file-input");
        this._spinner = $('#id_spinner');

        //EVENTI
        this._btnSalva.on('click',this._onClickBtnSalva.bind(this));
        this._btnScarica.on('click',this._onClickBtnScarica.bind(this));
        this._customFileInput.on("change", this._onCustomFileChange.bind(this))

        //CAricamento iniziale
        this._loadUploadDATManager();
    };

    /*
     * Pattern Singleton
     * @return Ritorna l'istanza dell'oggetto
     */
    UploadDATManager.getInstance = function() {
        if (_this === null) _this = new UploadDATManager();
        return _this;
    };

    /***********************************************************************************************************************
     * HANDLER/LISTENER
     * **********************************************************************************************************************/

    /*
    * Handler evento click btn salva
    */
    UploadDATManager.prototype._onClickBtnSalva = function(e){
        if(this._controlliEstensioneAllegato()){
            this._salvaGTF();
        }
    }

    /*
    * Handler evento click btn scarica
    */
    UploadDATManager.prototype._onClickBtnScarica = function(e){
        if(this._inpIdArchivio.val()){
            window.open("/scaricagtfs/" + this._inpIdArchivio.val(), "_blank");
        }else{
            alert('Inserire un id');
        }
    }

    UploadDATManager.prototype._onCustomFileChange = function(e){
        let fileName = this._customFileInput.val().split("\\").pop();
        if (fileName === '') {
            this._customFileInput.siblings(".custom-file-label").addClass("selected").html('Scegli file:');
        } else {
            this._customFileInput.siblings(".custom-file-label").addClass("selected").html(fileName);
        }
    }

    /***********************************************************************************************************************
     * PRIVATE FUNCTION
     * **********************************************************************************************************************/

    UploadDATManager.prototype._loadUploadDATManager = function(e){
        console.log('caricamento');
    }

    /*
    * Handler evento click btn salva
    */
    UploadDATManager.prototype._salvaGTF = function(e){
        let form = this._form[0]
        let data = new FormData(form);
        data.append("allegato", this._inputFile.val());
        this._showSpinner();
        try {
            $.ajax({
                type: "POST",
                enctype: 'multipart/form-data',
                url: '/uploaddat',
                data: data,
                processData: false,
                contentType: false,
                cache: false,
                timeout: 600000,
                context: this,
                success: function(ret) {
                    this._hideSpinner();
                    let parseRet = JSON.parse(ret);
                    if(parseRet['ESITO']['number'] === 0){
                        alert('Salvataggio eseguito correttamente');
                        this._inputFile.val('').trigger('change');
                    }else{
                        alert(parseRet['ESITO']['description']);
                    }
                },
                error: function(xhr, textStatus, errorThrown) {
                    this._hideSpinner();
                    alert(errorThrown);
                }
            });
        } catch (error) {
            this._hideSpinner();
            alert(error);
        }
    }

    UploadDATManager.prototype._controlliEstensioneAllegato = function() {
        let nomeFile = this._inputFile.val();
        if (nomeFile === '') {
            alert("Nessun file selezionato");
            return false;
        } else {
            let parti = nomeFile.split('.');
            let estensione = parti[parti.length - 1];
            estensione = estensione.toLowerCase();
        }
        return true;
    };

    UploadDATManager.prototype._showSpinner = function() {
        this._spinner.removeClass('d-none');
        this._btnSalva.prop('disabled',true);
    };

    UploadDATManager.prototype._hideSpinner = function() {
        this._spinner.addClass('d-none');
        this._btnSalva.prop('disabled',false);
    };

})();