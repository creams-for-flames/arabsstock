#!/usr/bin/ruby

require 'bundler/inline'

# update system ruby
# sudo apt-add-repository ppa:brightbox/ruby-ng
# sudo apt install ruby2.7
# sudo apt-get install ruby2.7-dev
# sudo gem update --system
# run it once as admin to install required gems

gemfile do
    source 'https://rubygems.org'
      gem 'prawn'
      gem 'prawn-table'
      gem 'json'
end

require 'prawn'
require 'json'

data = JSON.parse(ARGV[0])

base_folder = File::dirname(__FILE__);

Prawn::Document.generate(data['output']) do |pdf|

  pdf.font_families.update(
    "DejaVuSans" => {
      :bold        => base_folder + "/fonts/DejaVuSansCondensed-Bold.ttf",
      :italic      => base_folder + "/fonts/DejaVuSans-Oblique.ttf",
      :bold_italic => base_folder + "/fonts/DejaVuSansCondensed-BoldOblique.ttf",
      :normal      => base_folder + "/fonts/DejaVuSans.ttf"
    }
  )



  logopath = data['logo']
  initial_y = pdf.cursor
  initialmove_y = 35
  address_x = 35
  invoice_header_x = 325
  lineheight_y = 12
  font_size = 9

  pdf.move_down initialmove_y

  # Add the font style and size
  pdf.font "DejaVuSans"
  pdf.font_size font_size

  # title
  pdf.text_box data['logo_text'], :at => [0,  pdf.cursor], :size => 12
  pdf.move_down lineheight_y
  pdf.move_down lineheight_y
  pdf.move_down lineheight_y

  last_measured_y1 = pdf.cursor
  #start with EON Media Group
  pdf.text_box data['from_label'], :at => [0,  pdf.cursor], :style => :bold
  pdf.move_down 16
  pdf.text_box data['company'], :at => [0,  pdf.cursor]
  pdf.move_down lineheight_y
  pdf.text_box data['company_address_line1'], :at => [0,  pdf.cursor]
  pdf.move_down lineheight_y
  pdf.text_box data['company_address_line2'], :at => [0,  pdf.cursor]
  pdf.move_down lineheight_y

  last_measured_y = pdf.cursor
  pdf.move_cursor_to pdf.bounds.height

  pdf.image logopath, :width => 215, :position => :left

  pdf.move_cursor_to last_measured_y

  # client address
  pdf.move_down 36
  last_measured_y = pdf.cursor

  pdf.text_box data['for_label'], :at => [0,  pdf.cursor], :style => :bold
  pdf.move_down 16
  pdf.text_box data['client'], :at => [0,  pdf.cursor]
  if data['client_is_business'] == '1'
    pdf.move_down lineheight_y
    pdf.text_box data['client_company_address'], :at => [0,  pdf.cursor]
    pdf.move_down lineheight_y
    pdf.text_box data['client_company_contact'], :at => [0,  pdf.cursor]
    pdf.move_down lineheight_y
    pdf.text_box data['client_company_tax_id'], :at => [0,  pdf.cursor]
  else
    pdf.move_down lineheight_y
    pdf.text_box data['client_contact_name'], :at => [0,  pdf.cursor]
  end

  pdf.move_cursor_to last_measured_y1

  invoice_header_data = [
    [data['invoice_label'], data['invoice_id']],
    [data['invoice_date_label'], data['invoice_date']],
    [data['payment_method_label'], data['payment_method']],
    [data['payment_status_label'], data['payment_status']],
    [data['amount_due_label'], data['amount_due']]
  ]

  pdf.text_box data['details_label'], :at => [275,  pdf.cursor], :style => :bold
  pdf.move_down 16

  pdf.table(invoice_header_data, :position => 275, :width => 265) do
    style(row(0..3).columns(0..1), :padding => [0, 1, 5, 1], :borders => [])
    style(row(4), :background_color => 'e9e9e9', :border_color => 'dddddd', :font_style => :bold)
    style(column(1), :align => :right)
    style(row(4).columns(0), :borders => [:top, :left, :bottom])
    style(row(4).columns(1), :borders => [:top, :right, :bottom])
  end

  pdf.move_down 85

  invoice_services_data_header = [data['item_label'], " ", " ", " ", data['price_label']]
  invoice_services_data = [
    invoice_services_data_header,
  ] + data['items'] + [[" ", " ", " ", " ", " "]]

  pdf.table(invoice_services_data, :width => pdf.bounds.width) do
    style(row(1..-1).columns(0..-1), :padding => [4, 5, 4, 5], :borders => [:bottom], :border_color => 'dddddd')
    style(row(0), :background_color => 'e9e9e9', :border_color => 'dddddd', :font_style => :bold)
    style(row(0).columns(0..-1), :borders => [:top, :bottom])
    style(row(0).columns(0), :borders => [:top, :left, :bottom])
    style(row(0).columns(-1), :borders => [:top, :right, :bottom])
    style(row(-1), :border_width => 2)
    style(column(2..-1), :align => :right)
    style(columns(0), :width => 375)
    style(columns(1), :width => 75)
  end

  pdf.move_down 1

  invoice_services_totals_data = [
    [data['total_label'], data['total']],
    [data['amount_paid_label'], data['amount_paid']]
  ]

  pdf.table(invoice_services_totals_data, :position => invoice_header_x, :width => 215) do
    style(row(0..1).columns(0..1), :padding => [1, 5, 1, 5], :borders => [])
    style(row(0), :font_style => :bold)
    # style(row(2), :background_color => 'e9e9e9', :border_color => 'dddddd', :font_style => :bold)
    style(column(1), :align => :right)
    # style(row(2).columns(0), :borders => [:top, :left, :bottom])
    # style(row(2).columns(1), :borders => [:top, :right, :bottom])
  end

  pdf.move_down 25

  invoice_terms_data = [
    [data['terms_label']],
    [data['terms_value']]
  ]

  pdf.table(invoice_terms_data, :width => 275) do
    style(row(0..-1).columns(0..-1), :padding => [1, 0, 1, 0], :borders => [])
    style(row(0).columns(0), :font_style => :bold)
  end

  pdf.move_down 15

  invoice_notes_data = [
    [data['notes_label']],
    [data['notes_value']]
  ]

  pdf.table(invoice_notes_data, :width => 275) do
    style(row(0..-1).columns(0..-1), :padding => [1, 0, 1, 0], :borders => [])
    style(row(0).columns(0), :font_style => :bold)
  end

end
