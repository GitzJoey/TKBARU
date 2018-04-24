@extends('layouts.codebase.blank')

@section('title')
@endsection

@section('custom_css')
@endsection

@section('content')
    <div id="test1">
        <div class="input-group">
            <flat-pickr v-model="date" class="form-control"></flat-pickr>
        </div>
    </div>

    <br><br><br>

    <div id="test2">
        <vue-autonumeric v-model="aa" v-bind:options="{
            digitGroupSeparator: '.',
            decimalCharacter: ',',
            decimalCharacterAlternative: '.',
            currencySymbol: '\u00a0€',
            currencySymbolPlacement: 's',
            roundingMethod: 'U',
            minimumValue: '0'}"></vue-autonumeric>
    </div>

    <br><br><br>

    <div id="test3">

    </div>
@endsection

@section('custom_js')
    <script type="application/javascript">
        var test1 = new Vue({
            el: '#test1',
            data: {
                date:''
            }
        });

        var test2 = new Vue({
            el: '#test2',
            data: {

            }
        });

        var test3 = new Vue({
            el: '#test3',
            data: {

            }
        });


    </script>
@endsection