@if(!isset($PDF))
<div class="joosa top_section">
	<header id="header">
		<div id="trueHeader">
			<div class="wrapper">
				<div class="container">
					<div class="logo_main">
						<a href="{{URL::to('/')}}" id="">
							<img src="{{asset('/protectodiez/sgmmPNG/gastosmedicosmayores180.png')}}" alt="Seguro de gastos medicos mayores" >
						</a>
					</div>
					<div class="menu_main">
						<div class="joosa top_links">
							Llámanos: <strong>(33) 200-201-70</strong>
							<a href="{{URL::to('/cotizador')}}" class="makeap_but">Cotiza ahora</a>
						</div>
						<nav id="access" class="access" role="navigation">
							<div id="menu" class="menu">
								<ul id="tiny">
									{{$menuSitio}}
								</ul>
							</div>
						</nav>
					</div>
				</div>
			</div>
		</div>
	</header>
</div>
<div class="clearfix mar_top13"></div>
@endif
