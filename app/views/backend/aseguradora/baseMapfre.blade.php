
@section('contenido')
	<form id="frmBase" class="form-horizontal">
		<div class="form-group">
			<label class="col-md-3 control-label">Suma asegurada:</label>
			<div class="col-md-4">
				<select id="sa" name="sa" class="form-control">
					<option value="5000000" {{(($baseMapfre->sa==5000000) ? 'selected' : '')}}>$5,000,000.00</option>
					<option value="10000000" {{(($baseMapfre->sa==10000000) ? 'selected' : '')}}>$10,000,000.00</option>
					<option value="15000000" {{(($baseMapfre->sa==15000000) ? 'selected' : '')}}>$15,000,000.00</option>
					<option value="20000000" {{(($baseMapfre->sa==20000000) ? 'selected' : '')}}>$20,000,000.00</option>
					<option value="25000000" {{(($baseMapfre->sa==25000000) ? 'selected' : '')}}>$25,000,000.00</option>
					<option value="40000000" {{(($baseMapfre->sa==40000000) ? 'selected' : '')}}>$40,000,000.00</option>
					<option value="100000000" {{(($baseMapfre->sa==100000000) ? 'selected' : '')}}>$100,000,000.00</option>
					<option value="130000000" {{(($baseMapfre->sa==130000000) ? 'selected' : '')}}>$130,000,000.00</option>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label class="col-md-3 control-label">Deducible 0-19</label>
			<div class="col-md-4">
				<select id="deducible_19" name="deducible_19" class="form-control">
					<option value="10000" {{(($baseMapfre->deducible_19==10000) ? 'selected' : '')}}>$10,000.00</option>
					<option value="11000" {{(($baseMapfre->deducible_19==11000) ? 'selected' : '')}}>$11,000.00</option>
					<option value="12000" {{(($baseMapfre->deducible_19==12000) ? 'selected' : '')}}>$12,000.00</option>
					<option value="13000" {{(($baseMapfre->deducible_19==13000) ? 'selected' : '')}}>$13,000.00</option>
					<option value="14000" {{(($baseMapfre->deducible_19==14000) ? 'selected' : '')}}>$14,000.00</option>
					<option value="15000" {{(($baseMapfre->deducible_19==15000) ? 'selected' : '')}}>$15,000.00</option>
					<option value="16000" {{(($baseMapfre->deducible_19==16000) ? 'selected' : '')}}>$16,000.00</option>
					<option value="17000" {{(($baseMapfre->deducible_19==17000) ? 'selected' : '')}}>$17,000.00</option>
					<option value="18000" {{(($baseMapfre->deducible_19==18000) ? 'selected' : '')}}>$18,000.00</option>
					<option value="19000" {{(($baseMapfre->deducible_19==19000) ? 'selected' : '')}}>$19,000.00</option>
					<option value="20000" {{(($baseMapfre->deducible_19==20000) ? 'selected' : '')}}>$20,000.00</option>
					<option value="25000" {{(($baseMapfre->deducible_19==25000) ? 'selected' : '')}}>$25,000.00</option>
					<option value="30000" {{(($baseMapfre->deducible_19==30000) ? 'selected' : '')}}>$30,000.00</option>
					<option value="40000" {{(($baseMapfre->deducible_19==40000) ? 'selected' : '')}}>$40,000.00</option>
					<option value="50000" {{(($baseMapfre->deducible_19==50000) ? 'selected' : '')}}>$50,000.00</option>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label class="col-md-3 control-label">Deducible 20-24</label>
			<div class="col-md-4">
				<select id="deducible_24" name="deducible_24" class="form-control">
					<option value="10000" {{(($baseMapfre->deducible_24==10000) ? 'selected' : '')}}>$10,000.00</option>
					<option value="11000" {{(($baseMapfre->deducible_24==11000) ? 'selected' : '')}}>$11,000.00</option>
					<option value="12000" {{(($baseMapfre->deducible_24==12000) ? 'selected' : '')}}>$12,000.00</option>
					<option value="13000" {{(($baseMapfre->deducible_24==13000) ? 'selected' : '')}}>$13,000.00</option>
					<option value="14000" {{(($baseMapfre->deducible_24==14000) ? 'selected' : '')}}>$14,000.00</option>
					<option value="15000" {{(($baseMapfre->deducible_24==15000) ? 'selected' : '')}}>$15,000.00</option>
					<option value="16000" {{(($baseMapfre->deducible_24==16000) ? 'selected' : '')}}>$16,000.00</option>
					<option value="17000" {{(($baseMapfre->deducible_24==17000) ? 'selected' : '')}}>$17,000.00</option>
					<option value="18000" {{(($baseMapfre->deducible_24==18000) ? 'selected' : '')}}>$18,000.00</option>
					<option value="19000" {{(($baseMapfre->deducible_24==19000) ? 'selected' : '')}}>$19,000.00</option>
					<option value="20000" {{(($baseMapfre->deducible_24==20000) ? 'selected' : '')}}>$20,000.00</option>
					<option value="25000" {{(($baseMapfre->deducible_24==25000) ? 'selected' : '')}}>$25,000.00</option>
					<option value="30000" {{(($baseMapfre->deducible_24==30000) ? 'selected' : '')}}>$30,000.00</option>
					<option value="40000" {{(($baseMapfre->deducible_24==40000) ? 'selected' : '')}}>$40,000.00</option>
					<option value="50000" {{(($baseMapfre->deducible_24==50000) ? 'selected' : '')}}>$50,000.00</option>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label class="col-md-3 control-label">Deducible 25-29</label>
			<div class="col-md-4">
				<select id="deducible_29" name="deducible_29" class="form-control">
					<option value="10000" {{(($baseMapfre->deducible_29==10000) ? 'selected' : '')}}>$10,000.00</option>
					<option value="11000" {{(($baseMapfre->deducible_29==11000) ? 'selected' : '')}}>$11,000.00</option>
					<option value="12000" {{(($baseMapfre->deducible_29==12000) ? 'selected' : '')}}>$12,000.00</option>
					<option value="13000" {{(($baseMapfre->deducible_29==13000) ? 'selected' : '')}}>$13,000.00</option>
					<option value="14000" {{(($baseMapfre->deducible_29==14000) ? 'selected' : '')}}>$14,000.00</option>
					<option value="15000" {{(($baseMapfre->deducible_29==15000) ? 'selected' : '')}}>$15,000.00</option>
					<option value="16000" {{(($baseMapfre->deducible_29==16000) ? 'selected' : '')}}>$16,000.00</option>
					<option value="17000" {{(($baseMapfre->deducible_29==17000) ? 'selected' : '')}}>$17,000.00</option>
					<option value="18000" {{(($baseMapfre->deducible_29==18000) ? 'selected' : '')}}>$18,000.00</option>
					<option value="19000" {{(($baseMapfre->deducible_29==19000) ? 'selected' : '')}}>$19,000.00</option>
					<option value="20000" {{(($baseMapfre->deducible_29==20000) ? 'selected' : '')}}>$20,000.00</option>
					<option value="25000" {{(($baseMapfre->deducible_29==25000) ? 'selected' : '')}}>$25,000.00</option>
					<option value="30000" {{(($baseMapfre->deducible_29==30000) ? 'selected' : '')}}>$30,000.00</option>
					<option value="40000" {{(($baseMapfre->deducible_29==40000) ? 'selected' : '')}}>$40,000.00</option>
					<option value="50000" {{(($baseMapfre->deducible_29==50000) ? 'selected' : '')}}>$50,000.00</option>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label class="col-md-3 control-label">Deducible 29-34</label>
			<div class="col-md-4">
				<select id="deducible_34" name="deducible_34" class="form-control">
					<option value="10000" {{(($baseMapfre->deducible_34==10000) ? 'selected' : '')}}>$10,000.00</option>
					<option value="11000" {{(($baseMapfre->deducible_34==11000) ? 'selected' : '')}}>$11,000.00</option>
					<option value="12000" {{(($baseMapfre->deducible_34==12000) ? 'selected' : '')}}>$12,000.00</option>
					<option value="13000" {{(($baseMapfre->deducible_34==13000) ? 'selected' : '')}}>$13,000.00</option>
					<option value="14000" {{(($baseMapfre->deducible_34==14000) ? 'selected' : '')}}>$14,000.00</option>
					<option value="15000" {{(($baseMapfre->deducible_34==15000) ? 'selected' : '')}}>$15,000.00</option>
					<option value="16000" {{(($baseMapfre->deducible_34==16000) ? 'selected' : '')}}>$16,000.00</option>
					<option value="17000" {{(($baseMapfre->deducible_34==17000) ? 'selected' : '')}}>$17,000.00</option>
					<option value="18000" {{(($baseMapfre->deducible_34==18000) ? 'selected' : '')}}>$18,000.00</option>
					<option value="19000" {{(($baseMapfre->deducible_34==19000) ? 'selected' : '')}}>$19,000.00</option>
					<option value="20000" {{(($baseMapfre->deducible_34==20000) ? 'selected' : '')}}>$20,000.00</option>
					<option value="25000" {{(($baseMapfre->deducible_34==25000) ? 'selected' : '')}}>$25,000.00</option>
					<option value="30000" {{(($baseMapfre->deducible_34==30000) ? 'selected' : '')}}>$30,000.00</option>
					<option value="40000" {{(($baseMapfre->deducible_34==40000) ? 'selected' : '')}}>$40,000.00</option>
					<option value="50000" {{(($baseMapfre->deducible_34==50000) ? 'selected' : '')}}>$50,000.00</option>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label class="col-md-3 control-label">Deducible 35-39</label>
			<div class="col-md-4">
				<select id="deducible_39" name="deducible_39" class="form-control">
					<option value="10000" {{(($baseMapfre->deducible_39==10000) ? 'selected' : '')}}>$10,000.00</option>
					<option value="11000" {{(($baseMapfre->deducible_39==11000) ? 'selected' : '')}}>$11,000.00</option>
					<option value="12000" {{(($baseMapfre->deducible_39==12000) ? 'selected' : '')}}>$12,000.00</option>
					<option value="13000" {{(($baseMapfre->deducible_39==13000) ? 'selected' : '')}}>$13,000.00</option>
					<option value="14000" {{(($baseMapfre->deducible_39==14000) ? 'selected' : '')}}>$14,000.00</option>
					<option value="15000" {{(($baseMapfre->deducible_39==15000) ? 'selected' : '')}}>$15,000.00</option>
					<option value="16000" {{(($baseMapfre->deducible_39==16000) ? 'selected' : '')}}>$16,000.00</option>
					<option value="17000" {{(($baseMapfre->deducible_39==17000) ? 'selected' : '')}}>$17,000.00</option>
					<option value="18000" {{(($baseMapfre->deducible_39==18000) ? 'selected' : '')}}>$18,000.00</option>
					<option value="19000" {{(($baseMapfre->deducible_39==19000) ? 'selected' : '')}}>$19,000.00</option>
					<option value="20000" {{(($baseMapfre->deducible_39==20000) ? 'selected' : '')}}>$20,000.00</option>
					<option value="25000" {{(($baseMapfre->deducible_39==25000) ? 'selected' : '')}}>$25,000.00</option>
					<option value="30000" {{(($baseMapfre->deducible_39==30000) ? 'selected' : '')}}>$30,000.00</option>
					<option value="40000" {{(($baseMapfre->deducible_39==40000) ? 'selected' : '')}}>$40,000.00</option>
					<option value="50000" {{(($baseMapfre->deducible_39==50000) ? 'selected' : '')}}>$50,000.00</option>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label class="col-md-3 control-label">Deducible 39-44</label>
			<div class="col-md-4">
				<select id="deducible_44" name="deducible_44" class="form-control">
					<option value="10000" {{(($baseMapfre->deducible_44==10000) ? 'selected' : '')}}>$10,000.00</option>
					<option value="11000" {{(($baseMapfre->deducible_44==11000) ? 'selected' : '')}}>$11,000.00</option>
					<option value="12000" {{(($baseMapfre->deducible_44==12000) ? 'selected' : '')}}>$12,000.00</option>
					<option value="13000" {{(($baseMapfre->deducible_44==13000) ? 'selected' : '')}}>$13,000.00</option>
					<option value="14000" {{(($baseMapfre->deducible_44==14000) ? 'selected' : '')}}>$14,000.00</option>
					<option value="15000" {{(($baseMapfre->deducible_44==15000) ? 'selected' : '')}}>$15,000.00</option>
					<option value="16000" {{(($baseMapfre->deducible_44==16000) ? 'selected' : '')}}>$16,000.00</option>
					<option value="17000" {{(($baseMapfre->deducible_44==17000) ? 'selected' : '')}}>$17,000.00</option>
					<option value="18000" {{(($baseMapfre->deducible_44==18000) ? 'selected' : '')}}>$18,000.00</option>
					<option value="19000" {{(($baseMapfre->deducible_44==19000) ? 'selected' : '')}}>$19,000.00</option>
					<option value="20000" {{(($baseMapfre->deducible_44==20000) ? 'selected' : '')}}>$20,000.00</option>
					<option value="25000" {{(($baseMapfre->deducible_44==25000) ? 'selected' : '')}}>$25,000.00</option>
					<option value="30000" {{(($baseMapfre->deducible_44==30000) ? 'selected' : '')}}>$30,000.00</option>
					<option value="40000" {{(($baseMapfre->deducible_44==40000) ? 'selected' : '')}}>$40,000.00</option>
					<option value="50000" {{(($baseMapfre->deducible_44==50000) ? 'selected' : '')}}>$50,000.00</option>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label class="col-md-3 control-label">Deducible 45-49</label>
			<div class="col-md-4">
				<select id="deducible_49" name="deducible_49" class="form-control">
					<option value="10000" {{(($baseMapfre->deducible_49==10000) ? 'selected' : '')}}>$10,000.00</option>
					<option value="11000" {{(($baseMapfre->deducible_49==11000) ? 'selected' : '')}}>$11,000.00</option>
					<option value="12000" {{(($baseMapfre->deducible_49==12000) ? 'selected' : '')}}>$12,000.00</option>
					<option value="13000" {{(($baseMapfre->deducible_49==13000) ? 'selected' : '')}}>$13,000.00</option>
					<option value="14000" {{(($baseMapfre->deducible_49==14000) ? 'selected' : '')}}>$14,000.00</option>
					<option value="15000" {{(($baseMapfre->deducible_49==15000) ? 'selected' : '')}}>$15,000.00</option>
					<option value="16000" {{(($baseMapfre->deducible_49==16000) ? 'selected' : '')}}>$16,000.00</option>
					<option value="17000" {{(($baseMapfre->deducible_49==17000) ? 'selected' : '')}}>$17,000.00</option>
					<option value="18000" {{(($baseMapfre->deducible_49==18000) ? 'selected' : '')}}>$18,000.00</option>
					<option value="19000" {{(($baseMapfre->deducible_49==19000) ? 'selected' : '')}}>$19,000.00</option>
					<option value="20000" {{(($baseMapfre->deducible_49==20000) ? 'selected' : '')}}>$20,000.00</option>
					<option value="25000" {{(($baseMapfre->deducible_49==25000) ? 'selected' : '')}}>$25,000.00</option>
					<option value="30000" {{(($baseMapfre->deducible_49==30000) ? 'selected' : '')}}>$30,000.00</option>
					<option value="40000" {{(($baseMapfre->deducible_49==40000) ? 'selected' : '')}}>$40,000.00</option>
					<option value="50000" {{(($baseMapfre->deducible_49==50000) ? 'selected' : '')}}>$50,000.00</option>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label class="col-md-3 control-label">Deducible 49-54</label>
			<div class="col-md-4">
				<select id="deducible_54" name="deducible_54" class="form-control">
					<option value="10000" {{(($baseMapfre->deducible_54==10000) ? 'selected' : '')}}>$10,000.00</option>
					<option value="11000" {{(($baseMapfre->deducible_54==11000) ? 'selected' : '')}}>$11,000.00</option>
					<option value="12000" {{(($baseMapfre->deducible_54==12000) ? 'selected' : '')}}>$12,000.00</option>
					<option value="13000" {{(($baseMapfre->deducible_54==13000) ? 'selected' : '')}}>$13,000.00</option>
					<option value="14000" {{(($baseMapfre->deducible_54==14000) ? 'selected' : '')}}>$14,000.00</option>
					<option value="15000" {{(($baseMapfre->deducible_54==15000) ? 'selected' : '')}}>$15,000.00</option>
					<option value="16000" {{(($baseMapfre->deducible_54==16000) ? 'selected' : '')}}>$16,000.00</option>
					<option value="17000" {{(($baseMapfre->deducible_54==17000) ? 'selected' : '')}}>$17,000.00</option>
					<option value="18000" {{(($baseMapfre->deducible_54==18000) ? 'selected' : '')}}>$18,000.00</option>
					<option value="19000" {{(($baseMapfre->deducible_54==19000) ? 'selected' : '')}}>$19,000.00</option>
					<option value="20000" {{(($baseMapfre->deducible_54==20000) ? 'selected' : '')}}>$20,000.00</option>
					<option value="25000" {{(($baseMapfre->deducible_54==25000) ? 'selected' : '')}}>$25,000.00</option>
					<option value="30000" {{(($baseMapfre->deducible_54==30000) ? 'selected' : '')}}>$30,000.00</option>
					<option value="40000" {{(($baseMapfre->deducible_54==40000) ? 'selected' : '')}}>$40,000.00</option>
					<option value="50000" {{(($baseMapfre->deducible_54==50000) ? 'selected' : '')}}>$50,000.00</option>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label class="col-md-3 control-label">Deducible 54-59</label>
			<div class="col-md-4">
				<select id="deducible_59" name="deducible_59" class="form-control">
					<option value="10000" {{(($baseMapfre->deducible_59==10000) ? 'selected' : '')}}>$10,000.00</option>
					<option value="11000" {{(($baseMapfre->deducible_59==11000) ? 'selected' : '')}}>$11,000.00</option>
					<option value="12000" {{(($baseMapfre->deducible_59==12000) ? 'selected' : '')}}>$12,000.00</option>
					<option value="13000" {{(($baseMapfre->deducible_59==13000) ? 'selected' : '')}}>$13,000.00</option>
					<option value="14000" {{(($baseMapfre->deducible_59==14000) ? 'selected' : '')}}>$14,000.00</option>
					<option value="15000" {{(($baseMapfre->deducible_59==15000) ? 'selected' : '')}}>$15,000.00</option>
					<option value="16000" {{(($baseMapfre->deducible_59==16000) ? 'selected' : '')}}>$16,000.00</option>
					<option value="17000" {{(($baseMapfre->deducible_59==17000) ? 'selected' : '')}}>$17,000.00</option>
					<option value="18000" {{(($baseMapfre->deducible_59==18000) ? 'selected' : '')}}>$18,000.00</option>
					<option value="19000" {{(($baseMapfre->deducible_59==19000) ? 'selected' : '')}}>$19,000.00</option>
					<option value="20000" {{(($baseMapfre->deducible_59==20000) ? 'selected' : '')}}>$20,000.00</option>
					<option value="25000" {{(($baseMapfre->deducible_59==25000) ? 'selected' : '')}}>$25,000.00</option>
					<option value="30000" {{(($baseMapfre->deducible_59==30000) ? 'selected' : '')}}>$30,000.00</option>
					<option value="40000" {{(($baseMapfre->deducible_59==40000) ? 'selected' : '')}}>$40,000.00</option>
					<option value="50000" {{(($baseMapfre->deducible_59==50000) ? 'selected' : '')}}>$50,000.00</option>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label class="col-md-3 control-label">Deducible 60-64</label>
			<div class="col-md-4">
				<select id="deducible_64" name="deducible_64" class="form-control">
					<option value="10000" {{(($baseMapfre->deducible_64==10000) ? 'selected' : '')}}>$10,000.00</option>
					<option value="11000" {{(($baseMapfre->deducible_64==11000) ? 'selected' : '')}}>$11,000.00</option>
					<option value="12000" {{(($baseMapfre->deducible_64==12000) ? 'selected' : '')}}>$12,000.00</option>
					<option value="13000" {{(($baseMapfre->deducible_64==13000) ? 'selected' : '')}}>$13,000.00</option>
					<option value="14000" {{(($baseMapfre->deducible_64==14000) ? 'selected' : '')}}>$14,000.00</option>
					<option value="15000" {{(($baseMapfre->deducible_64==15000) ? 'selected' : '')}}>$15,000.00</option>
					<option value="16000" {{(($baseMapfre->deducible_64==16000) ? 'selected' : '')}}>$16,000.00</option>
					<option value="17000" {{(($baseMapfre->deducible_64==17000) ? 'selected' : '')}}>$17,000.00</option>
					<option value="18000" {{(($baseMapfre->deducible_64==18000) ? 'selected' : '')}}>$18,000.00</option>
					<option value="19000" {{(($baseMapfre->deducible_64==19000) ? 'selected' : '')}}>$19,000.00</option>
					<option value="20000" {{(($baseMapfre->deducible_64==20000) ? 'selected' : '')}}>$20,000.00</option>
					<option value="25000" {{(($baseMapfre->deducible_64==25000) ? 'selected' : '')}}>$25,000.00</option>
					<option value="30000" {{(($baseMapfre->deducible_64==30000) ? 'selected' : '')}}>$30,000.00</option>
					<option value="40000" {{(($baseMapfre->deducible_64==40000) ? 'selected' : '')}}>$40,000.00</option>
					<option value="50000" {{(($baseMapfre->deducible_64==50000) ? 'selected' : '')}}>$50,000.00</option>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label class="col-md-3 control-label">Deducible 65-69</label>
			<div class="col-md-4">
				<select id="deducible_69" name="deducible_69" class="form-control">
					<option value="10000" {{(($baseMapfre->deducible_69==10000) ? 'selected' : '')}}>$10,000.00</option>
					<option value="11000" {{(($baseMapfre->deducible_69==11000) ? 'selected' : '')}}>$11,000.00</option>
					<option value="12000" {{(($baseMapfre->deducible_69==12000) ? 'selected' : '')}}>$12,000.00</option>
					<option value="13000" {{(($baseMapfre->deducible_69==13000) ? 'selected' : '')}}>$13,000.00</option>
					<option value="14000" {{(($baseMapfre->deducible_69==14000) ? 'selected' : '')}}>$14,000.00</option>
					<option value="15000" {{(($baseMapfre->deducible_69==15000) ? 'selected' : '')}}>$15,000.00</option>
					<option value="16000" {{(($baseMapfre->deducible_69==16000) ? 'selected' : '')}}>$16,000.00</option>
					<option value="17000" {{(($baseMapfre->deducible_69==17000) ? 'selected' : '')}}>$17,000.00</option>
					<option value="18000" {{(($baseMapfre->deducible_69==18000) ? 'selected' : '')}}>$18,000.00</option>
					<option value="19000" {{(($baseMapfre->deducible_69==19000) ? 'selected' : '')}}>$19,000.00</option>
					<option value="20000" {{(($baseMapfre->deducible_69==20000) ? 'selected' : '')}}>$20,000.00</option>
					<option value="25000" {{(($baseMapfre->deducible_69==25000) ? 'selected' : '')}}>$25,000.00</option>
					<option value="30000" {{(($baseMapfre->deducible_69==30000) ? 'selected' : '')}}>$30,000.00</option>
					<option value="40000" {{(($baseMapfre->deducible_69==40000) ? 'selected' : '')}}>$40,000.00</option>
					<option value="50000" {{(($baseMapfre->deducible_69==50000) ? 'selected' : '')}}>$50,000.00</option>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label class="col-md-3 control-label">Coaseguro</label>
			<div class="col-md-4">
				<input type="text" id="coaseguro" name="coaseguro" class="form-control" value="{{$baseMapfre->coaseguro}}">
			</div>
		</div>
		<div class="form-group">
			<label class="col-md-3 control-label">Tabulador</label>
			<div class="col-md-4">
				<select id="tabulador" name="tabulador" class="form-control">
					<option value="C" {{(($baseMapfre->tabulador=="C") ? 'selected' : '')}}>Básico</option>
					<option value="D" {{(($baseMapfre->tabulador=="D") ? 'selected' : '')}}>Normal</option>
					<option value="E" {{(($baseMapfre->tabulador=="E") ? 'selected' : '')}}>Medio</option>
					<option value="F" {{(($baseMapfre->tabulador=="F") ? 'selected' : '')}}>Alto</option>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label class="col-md-3 control-label">Emergencia en el extranjero</label>
			<div class="col-md-4">
				<input type="text" id="emergencia-extranjero" name="emergencia-extranjero" class="form-control" value="{{$baseMapfre->emergencia_extranjero}}">
			</div>
		</div>
		<div class="form-group">
			<label class="col-md-3 control-label">Maternidad</label>
			<div class="col-md-4">
				<select id="sa-maternidad" name="sa-maternidad" class="form-control">
					<option value="20000" {{(($baseMapfre->sa_maternidad==20000) ? 'selected' : '')}}>$20,000.00</option>
					<option value="25000" {{(($baseMapfre->sa_maternidad==25000) ? 'selected' : '')}}>$25,000.00</option>
					<option value="30000" {{(($baseMapfre->sa_maternidad==30000) ? 'selected' : '')}}>$30,000.00</option>
					<option value="35000" {{(($baseMapfre->sa_maternidad==35000) ? 'selected' : '')}}>$35,000.00</option>
					<option value="40000" {{(($baseMapfre->sa_maternidad==40000) ? 'selected' : '')}}>$40,000.00</option>
					<option value="45000" {{(($baseMapfre->sa_maternidad==45000) ? 'selected' : '')}}>$45,000.00</option>
					<option value="50000" {{(($baseMapfre->sa_maternidad==50000) ? 'selected' : '')}}>$50,000.00</option>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label class="col-md-3 control-label">Reduccion de deducible</label>
			<div class="col-md-4">
				<input type="checkbox" id="reduccion-deducible" name="reduccion-deducible" class="form-control" {{(($baseMapfre->reduccion_deducible==1) ? 'checked' : '')}}>
			</div>
		</div>
		<div class="form-group">
			<label class="col-md-3 control-label">Cobertura dental y  visión</label>
			<div class="col-md-4">
				<select id="dental" name="dental" class="form-control">
					<option value="" {{(($baseMapfre->dental=="") ? 'selected' : '')}}>Ninguno</option>
					<option value="plata" {{(($baseMapfre->dental=="plata") ? 'selected' : '')}}>Plata</option>
					<option value="oro" {{(($baseMapfre->dental=="oro") ? 'selected' : '')}}>Oro</option>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label class="col-md-3 control-label">Complicaciones de gastos no cubiertos</label>
			<div class="col-md-4">
				<input type="checkbox" id="complicaciones" name="complicaciones" class="form-control" {{(($baseMapfre->complicaciones==1) ? 'checked' : '')}}>
			</div>
		</div>
		<div class="form-group">
			<label class="col-md-3 control-label">Procedimientos de vanguardia</label>
			<div class="col-md-4">
				<input type="checkbox" id="vanguardia" name="vanguardia" class="form-control" {{(($baseMapfre->vanguardia==1) ? 'checked' : '')}}>
			</div>
		</div>
		<div class="form-group">
			<label class="col-md-3 control-label">Cobertura multiregion</label>
			<div class="col-md-4">
				<input type="checkbox" id="multiregion" name="multiregion" class="form-control" {{(($baseMapfre->multiregion==1) ? 'checked' : '')}}>
			</div>
		</div>
		<div class="form-group">
			<label class="col-md-3 control-label">Padecimientos preexistentes declarados</label>
			<div class="col-md-4">
				<input type="checkbox" id="preexistentes" name="preexistentes" class="form-control" {{(($baseMapfre->preexistentes==1) ? 'checked' : '')}}>
			</div>
		</div>
		<div class="form-group">
			<label class="col-md-3 control-label">Enfermedades catastroficas en el extranjero</label>
			<div class="col-md-4">
				<input type="checkbox" id="catastroficas" name="catastroficas" class="form-control" {{(($baseMapfre->catastroficas==1) ? 'checked' : '')}}>
			</div>
		</div>
		<div class="form-group">
			<label class="col-md-3 control-label">Cobertura funeraria</label>
			<div class="col-md-4">
				<input type="checkbox" id="funeraria" name="funeraria" class="form-control" {{(($baseMapfre->funeraria==1) ? 'checked' : '')}}>
			</div>
		</div>
		<div class="form-group">
			<div class="col-md-4 col-md-offset-3">
				<button type="submit" class="btn btn-primary" id="btnOk">Aceptar</button>
			</div>
		</div>
	</form>
@stop