# Use an official Python runtime as a parent image
FROM python:2.7-slim

# Set the working directory to /app
WORKDIR /app

# Copy requirements.txt into the container at /app
ADD requirements.txt /app

# Install any needed packages specified in requirements.txt
RUN pip install -r requirements.txt

# Copy app.py into the container at /app
ADD app.py /app

# Run app.py when the container launches
CMD ["python", "app.py"]